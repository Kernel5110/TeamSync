<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $participante = $user->participante;
        $equipo = $participante ? $participante->equipo : null;
        $eventos = Evento::all(); // For creating a team
        
        $allTeams = null;
        if ($user->hasRole('admin')) {
            $allTeams = Equipo::with(['participantes.user', 'evento'])->get();
        }

        return view('team', compact('equipo', 'eventos', 'allTeams'));
    }

    public function search(Request $request)
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

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('edit teams')) {
            abort(403);
        }

        $equipo = Equipo::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'evento_id' => 'required|exists:eventos,id',
        ]);

        $equipo->update([
            'nombre' => $request->nombre,
            'evento_id' => $request->evento_id,
        ]);

        return redirect()->route('team')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy($id)
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

        return redirect()->route('team')->with('success', 'Equipo eliminado correctamente.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'evento_id' => 'required|exists:eventos,id',
        ]);

        $user = Auth::user();
        $participante = $user->participante;

        if (!$participante) {
            return back()->with('error', 'Debes completar tu registro de participante primero.');
        }

        if ($participante->equipo_id) {
            return back()->with('error', 'Ya perteneces a un equipo.');
        }

        $evento = Evento::findOrFail($request->evento_id);
        
        // Validate Event Status
        if ($evento->status !== 'En Curso' && $evento->status !== 'Próximo') {
             return back()->with('error', 'El evento no está disponible para registros (Estado: ' . $evento->status . ').');
        }

        if ($evento->equipos()->count() >= $evento->capacidad) {
            return back()->with('error', 'El evento ha alcanzado su capacidad máxima de equipos.');
        }

        $equipo = Equipo::create([
            'nombre' => $request->nombre,
            'evento_id' => $request->evento_id,
        ]);

        // Assign creator to team with a default role
        $participante->update([
            'equipo_id' => $equipo->id,
            'rol' => 'Líder', // Default role
        ]);

        return redirect()->route('team')->with('success', 'Equipo creado exitosamente.');
    }

    public function addMember(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = Auth::user();
        $participante = $user->participante;
        $equipo = $participante->equipo;

        if (!$equipo) {
            return back()->with('error', 'No tienes un equipo.');
        }

        $newMemberUser = User::where('email', $request->email)->first();
        $newMemberParticipante = $newMemberUser->participante;

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

    public function removeMember(Request $request, $team_id)
    {
        $user = Auth::user();
        $equipo = Equipo::findOrFail($team_id);

        // Check permissions: Admin or Team Leader
        $isLeader = $user->participante && $user->participante->equipo_id == $equipo->id && $user->participante->rol == 'Líder';
        if (!$user->hasRole('admin') && !$isLeader) {
            abort(403, 'No tienes permiso para eliminar miembros.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $memberUser = User::findOrFail($request->user_id);
        $memberParticipante = $memberUser->participante;

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

    public function requestJoin($equipo_id)
    {
        $user = Auth::user();
        $participante = $user->participante;

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

    public function acceptJoin($solicitud_id)
    {
        $solicitud = \App\Models\Solicitud::findOrFail($solicitud_id);
        $equipo = $solicitud->equipo;
        
        // Check if current user is leader of the team
        $user = Auth::user();
        if (!$user->participante || $user->participante->equipo_id !== $equipo->id || $user->participante->rol !== 'Líder') {
             abort(403, 'No tienes permiso para aceptar solicitudes.');
        }

        $solicitud->update(['status' => 'accepted']);

        // Add user to team
        $solicitante = $solicitud->user->participante;
        if ($solicitante && !$solicitante->equipo_id) {
             $solicitante->update([
                'equipo_id' => $equipo->id,
                'rol' => 'Miembro',
            ]);
        }

        return back()->with('success', 'Solicitud aceptada.');
    }

    public function rejectJoin($solicitud_id)
    {
        $solicitud = \App\Models\Solicitud::findOrFail($solicitud_id);
        $equipo = $solicitud->equipo;

        // Check if current user is leader of the team
        $user = Auth::user();
        if (!$user->participante || $user->participante->equipo_id !== $equipo->id || $user->participante->rol !== 'Líder') {
             abort(403, 'No tienes permiso para rechazar solicitudes.');
        }

        $solicitud->update(['status' => 'rejected']);

        return back()->with('success', 'Solicitud rechazada.');
    }
}
