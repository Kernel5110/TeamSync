<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $user = Auth::user();
        $participante = $user->participant;
        $equipo = $participante ? $participante->equipo : null;
        // Only show upcoming events for team creation
        $eventos = Evento::where('fecha_inicio', '>', now())->get();
        
        $allTeams = null;
        if ($user->hasRole('admin')) {
            $allTeams = Equipo::with(['participantes.user', 'evento'])->paginate(10, ['*'], 'all_teams_page');
        }

        // Fetch other teams (teams the user is NOT part of)
        $otherTeamsQuery = Equipo::with(['evento', 'participantes.user']);
        if ($equipo) {
            $otherTeamsQuery->where('id', '!=', $equipo->id);
        }
        $otherTeams = $otherTeamsQuery->paginate(10, ['*'], 'other_teams_page');

        // Fetch pending requests if user is a leader
        $pendingRequests = [];
        if ($equipo && $participante->rol === 'Líder') {
            $pendingRequests = \App\Models\Solicitud::where('equipo_id', $equipo->id)
                ->where('status', 'pending')
                ->with('user.participant')
                ->get();
        }

        // Check if user has any pending request sent
        $myPendingRequest = null;
        if (!$equipo) {
             $myPendingRequest = \App\Models\Solicitud::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();
        }

        return view('team', compact('equipo', 'eventos', 'allTeams', 'otherTeams', 'pendingRequests', 'myPendingRequest'));
    }

    public function search(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\View\View
    {
        $query = $request->input('query');
        $teams = [];
        
        if ($query) {
            $teams = Equipo::where('nombre', 'LIKE', "%{$query}%")
                ->with(['evento', 'participantes'])
                ->get();
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.team_search_results', compact('teams'))->render()
            ]);
        }

        return view('team_search', compact('teams', 'query'));
    }

    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        if (!Auth::user()->can('edit teams')) {
            abort(403);
        }

        $equipo = Equipo::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'evento_id' => 'required|exists:eventos,id',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nombre' => $request->nombre,
            'evento_id' => $request->evento_id,
        ];

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('team-logos', 'public');
            $data['logo_path'] = $path;
        }

        $equipo->update($data);

        return redirect()->route('teams.index')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        if (!Auth::user()->can('delete teams')) {
            abort(403);
        }

        $equipo = Equipo::findOrFail($id);
        
        // Optional: Handle participants before deleting (e.g., set team_id to null)
        foreach ($equipo->participantes as $participante) {
            $participante->update(['equipo_id' => null, 'rol' => null]);
        }

        $equipo->delete();

        return redirect()->route('teams.index')->with('success', 'Equipo eliminado correctamente.');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'evento_id' => 'required|exists:eventos,id',
            'logo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $participante = $user->participant;

        if (!$participante) {
            return back()->with('error', 'Debes completar tu registro de participante primero.');
        }

        if ($participante->equipo_id) {
            return back()->with('error', 'Ya perteneces a un equipo.');
        }

        $evento = Evento::findOrFail($request->evento_id);
        
        // Validate Event Status
        // Validate Event Status - Only allow joining upcoming events
        if ($evento->fecha_inicio <= now()) {
             return back()->with('error', 'No puedes unirte a un evento que ya ha iniciado o finalizado.');
        }

        if ($evento->equipos()->count() >= $evento->capacidad) {
            return back()->with('error', 'El evento ha alcanzado su capacidad máxima de equipos.');
        }

        $data = [
            'nombre' => $request->nombre,
            'evento_id' => $request->evento_id,
        ];

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('team-logos', 'public');
            $data['logo_path'] = $path;
        }

        $equipo = Equipo::create($data);

        // Assign creator to team with a default role
        $participante->update([
            'equipo_id' => $equipo->id,
            'rol' => 'Líder', // Default role
        ]);

        return redirect()->route('teams.index')->with('success', 'Equipo creado exitosamente.');
    }

    public function addMember(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = Auth::user();
        $participante = $user->participant;
        $equipo = $participante->equipo;

        if (!$equipo) {
            return back()->with('error', 'No tienes un equipo.');
        }

        $newMemberUser = User::where('email', $request->email)->first();
        $newMemberParticipante = $newMemberUser->participant;

        if (!$newMemberParticipante) {
            return back()->with('error', 'El usuario no ha completado su registro como participante.');
        }

        if ($newMemberParticipante->equipo_id) {
            return back()->with('error', 'El usuario ya pertenece a un equipo.');
        }

        $newMemberParticipante->update([
            'equipo_id' => $equipo->id,
            'rol' => 'Miembro', // Default role for new members
        ]);

        return back()->with('success', 'Miembro agregado exitosamente.');
    }

    public function removeMember(Request $request, int $team_id): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $equipo = Equipo::findOrFail($team_id);

        // Check permissions: Admin or Team Leader
        $isLeader = $user->participant && $user->participant->equipo_id == $equipo->id && $user->participant->rol == 'Líder';
        if (!$user->hasRole('admin') && !$isLeader) {
            abort(403, 'No tienes permiso para eliminar miembros.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $memberUser = User::findOrFail($request->user_id);
        $memberParticipante = $memberUser->participant;

        if (!$memberParticipante || $memberParticipante->equipo_id !== $equipo->id) {
            return back()->with('error', 'El usuario no pertenece a este equipo.');
        }

        // Prevent removing the leader if they are the only one, or handle leader transfer (not implemented yet)
        // For now, just allow removing anyone, but maybe warn if it's the leader?
        // If leader leaves, team might be leaderless.
        // Let's just allow it for now as per request "administrar".

        $memberParticipante->update([
            'equipo_id' => null,
            'rol' => null,
        ]);

        return back()->with('success', 'Miembro eliminado del equipo.');
    }

    public function requestJoin(int $equipo_id): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $participante = $user->participant;

        if (!$participante) {
            return back()->with('error', 'Debes completar tu registro de participante primero.');
        }

        if ($user->hasRole('admin')) {
            return back()->with('error', 'Los administradores no pueden unirse a equipos.');
        }

        if ($participante->equipo_id) {
            return back()->with('error', 'Ya perteneces a un equipo.');
        }

        // Check if request already exists
        $existingRequest = \App\Models\Solicitud::where('user_id', $user->id)
            ->where('equipo_id', $equipo_id)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return back()->with('error', 'Ya has enviado una solicitud a este equipo.');
        }

        \App\Models\Solicitud::create([
            'user_id' => $user->id,
            'equipo_id' => $equipo_id,
        ]);

        return back()->with('success', 'Solicitud enviada exitosamente.');
    }

    public function acceptJoin(int $solicitud_id): \Illuminate\Http\RedirectResponse
    {
        $solicitud = \App\Models\Solicitud::findOrFail($solicitud_id);
        $equipo = $solicitud->equipo;
        
        // Check if current user is leader of the team
        $user = Auth::user();
        if (!$user->participant || $user->participant->equipo_id !== $equipo->id || $user->participant->rol !== 'Líder') {
             abort(403, 'No tienes permiso para aceptar solicitudes.');
        }

        $solicitud->update(['status' => 'accepted']);

        // Add user to team
        $solicitante = $solicitud->user->participant;
        if ($solicitante && !$solicitante->equipo_id) {
             $solicitante->update([
                'equipo_id' => $equipo->id,
                'rol' => 'Miembro',
            ]);
        }

        return back()->with('success', 'Solicitud aceptada.');
    }

    public function rejectJoin(int $solicitud_id): \Illuminate\Http\RedirectResponse
    {
        $solicitud = \App\Models\Solicitud::findOrFail($solicitud_id);
        $equipo = $solicitud->equipo;

        // Check if current user is leader of the team
        $user = Auth::user();
        if (!$user->participant || $user->participant->equipo_id !== $equipo->id || $user->participant->rol !== 'Líder') {
             abort(403, 'No tienes permiso para rechazar solicitudes.');
        }

        $solicitud->update(['status' => 'rejected']);

        return back()->with('success', 'Solicitud rechazada.');
    }
}
