<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Event;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $user = Auth::user();
        $participante = $user->participant;
        $equipo = $participante ? $participante->team : null;
        // Only show upcoming events for team creation
        $eventos = Event::where('fecha_fin', '>=', now())->get();
        
        $allTeams = null;
        if ($user->hasRole('admin')) {
            $allTeams = Team::with(['participants.user', 'event'])->paginate(10, ['*'], 'all_teams_page');
        }

        // Fetch other teams (teams the user is NOT part of)
        $otherTeamsQuery = Team::with(['event', 'participants.user']);
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
            $teams = Team::where('nombre', 'LIKE', "%{$query}%")
                ->with(['event', 'participants'])
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
        $equipo = Team::findOrFail($id);
        $user = Auth::user();

        // Allow Admin OR Team Leader of this team
        $isLeader = $user->participant && $user->participant->equipo_id == $equipo->id && $user->participant->rol == 'Líder';
        
        if (!$user->hasRole('admin') && !$isLeader) {
            abort(403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'evento_id' => 'required|exists:eventos,id',
            'logo' => 'nullable|image|max:2048',
            'project_name' => 'nullable|string|max:255',
            'project_description' => 'nullable|string',
            'technologies' => 'nullable|string',
            'github_repo' => 'nullable|url',
            'github_pages' => 'nullable|url',
        ]);

        $data = [
            'nombre' => $request->nombre,
            'evento_id' => $request->evento_id,
            'project_name' => $request->project_name,
            'project_description' => $request->project_description,
            'technologies' => $request->technologies,
            'github_repo' => $request->github_repo,
            'github_pages' => $request->github_pages,
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

        $equipo = Team::findOrFail($id);
        
        // Dissociate all members
        foreach ($equipo->participants as $participante) {
            $participante->update([
                'equipo_id' => null,
                'rol' => null
            ]);
        }

        // Hard delete the team
        $equipo->delete();

        \App\Services\AuditLogger::log('delete', Team::class, $id, "Equipo eliminado: {$equipo->nombre}");

        return redirect()->route('teams.index')->with('success', 'Equipo eliminado permanentemente.');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        \Illuminate\Support\Facades\Log::info('TeamController::store called', $request->all());
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

        $evento = Event::findOrFail($request->evento_id);
        
        // Validate Event Status
        // Validate Event Status - Only allow joining upcoming events
        if ($evento->fecha_fin < now()) {
             return back()->with('error', 'No puedes unirte a un evento que ya ha finalizado.');
        }

        // Check if user is already in an active event
        $activeEvent = Event::whereHas('equipos.participantes', function($q) use ($user) {
            $q->where('usuario_id', $user->id);
        })->where('status_manual', 'En Curso') // Check manual status
          ->orWhere(function($q) use ($user) {
              // Also check date-based status if manual is not set (or just check date range)
              // To be safe and consistent with getStatusAttribute, we should check if any event the user is in is currently "En Curso"
              $q->whereHas('equipos.participantes', function($sq) use ($user) {
                  $sq->where('usuario_id', $user->id);
              })->whereNull('status_manual')
                ->where('fecha_inicio', '<=', now())
                ->where('fecha_fin', '>=', now());
          })->first();

        // Simplified check using the model's accessor logic would be inefficient in query, 
        // so we stick to DB query. 
        // Logic: If user is in any team belonging to an event that is currently active.
        
        // Let's refine the query to be cleaner.
        $userTeams = \App\Models\Team::whereHas('participants', function($q) use ($user) {
            $q->where('usuario_id', $user->id);
        })->with('event')->get();

        foreach ($userTeams as $team) {
            if ($team->event && $team->event->status === 'En Curso') {
                return back()->with('error', 'Ya estás participando en un evento en curso (' . $team->event->nombre . '). No puedes unirte a otro.');
            }
        }

        if ($evento->teams()->count() >= $evento->capacidad) {
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

        $equipo = Team::create($data);

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
        $equipo = $participante->team;

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
        $equipo = Team::findOrFail($team_id);

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

        \App\Services\AuditLogger::log('remove_member', Team::class, $equipo->id, "Miembro eliminado del equipo {$equipo->nombre}: {$memberUser->email}");

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

        // Check active events
        $userTeams = \App\Models\Team::whereHas('participants', function($q) use ($user) {
            $q->where('usuario_id', $user->id);
        })->with('event')->get();

        foreach ($userTeams as $team) {
            if ($team->event && $team->event->status === 'En Curso') {
                return back()->with('error', 'Ya estás participando en un evento en curso (' . $team->event->nombre . ').');
            }
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
        $equipo = $solicitud->team;
        
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
        $equipo = $solicitud->team;

        // Check if current user is leader of the team
        $user = Auth::user();
        if (!$user->participant || $user->participant->equipo_id !== $equipo->id || $user->participant->rol !== 'Líder') {
             abort(403, 'No tienes permiso para rechazar solicitudes.');
        }

        $solicitud->update(['status' => 'rejected']);

        return back()->with('success', 'Solicitud rechazada.');
    }
}
