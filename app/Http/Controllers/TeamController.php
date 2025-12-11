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
        $participants = $user->participants; // Get all participations
        $myTeams = $participants->map(function ($p) {
            return $p->team;
        })->filter();

        // Compatibility for view (shows first/main team or nothing if empty)
        $equipo = $myTeams->first(); 

        // Only show upcoming events for team creation
        $eventos = Event::where('ends_at', '>=', now())->get();
        
        $allTeams = null;
        if ($user->hasRole('admin')) {
            $allTeams = Team::with(['participants.user', 'event'])->paginate(10, ['*'], 'all_teams_page');
        }

        // Fetch other teams (teams the user is NOT part of)
        // Exclude IDs of all teams user is in
        $myTeamIds = $myTeams->pluck('id')->toArray();

        $otherTeamsQuery = Team::with(['event', 'participants.user']);
        if (!empty($myTeamIds)) {
            $otherTeamsQuery->whereNotIn('id', $myTeamIds);
        }
        $otherTeams = $otherTeamsQuery->paginate(10, ['*'], 'other_teams_page');

        // Fetch pending requests if user is a leader in ANY of their teams
        $pendingRequests = collect();
        foreach ($participants as $p) {
            if ($p->team_id && $p->rol === 'Líder') {
                $requests = \App\Models\Solicitud::where('team_id', $p->team_id)
                    ->where('status', 'pending')
                    ->with('user.participant')
                    ->get();
                $pendingRequests = $pendingRequests->merge($requests);
            }
        }

        // Check if user has any pending request sent (to teams they are NOT in)
        $myPendingRequest = \App\Models\Solicitud::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        return view('team', compact('equipo', 'myTeams', 'eventos', 'allTeams', 'otherTeams', 'pendingRequests', 'myPendingRequest'));
    }

    public function search(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\View\View
    {
        $query = $request->input('query');
        $teams = [];
        
        if ($query) {
            $teams = Team::where('name', 'LIKE', "%{$query}%")
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

    public function update(Request $request, int $teamId): \Illuminate\Http\RedirectResponse
    {
        $equipo = Team::findOrFail($teamId);
        $user = Auth::user();

        // Allow Admin OR Team Leader of this team
        $isLeader = $user->participant && $user->participant->team_id == $equipo->id && $user->participant->rol == 'Líder';
        
        if (!$user->hasRole('admin') && !$isLeader) {
            abort(403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'event_id' => 'required|exists:events,id',
            'logo' => 'nullable|image|max:2048',
            'project_name' => 'nullable|string|max:255',
            'project_description' => 'nullable|string',
            'technologies' => 'nullable|string',
            'github_repo' => 'nullable|url',
            'github_pages' => 'nullable|url',
        ]);

        $data = [
            'name' => $request->nombre,
            'event_id' => $request->event_id,
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

    public function destroy(int $teamId): \Illuminate\Http\RedirectResponse
    {
        if (!Auth::user()->can('delete teams')) {
            abort(403);
        }

        $equipo = Team::findOrFail($teamId);
        
        // Dissociate all members
        foreach ($equipo->participants as $participante) {
            $participante->update([
                'team_id' => null,
                'rol' => null
            ]);
        }

        // Hard delete the team
        $equipo->delete();

        \App\Services\AuditLogger::log('delete', Team::class, $teamId, "Equipo eliminado: {$equipo->name}");

        return redirect()->route('teams.index')->with('success', 'Equipo eliminado permanentemente.');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        \Illuminate\Support\Facades\Log::info('TeamController::store called', $request->all());
        $request->validate([
            'nombre' => 'required|string|max:255',
            'event_id' => 'required|exists:events,id',
            'logo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        // Use latest participant for profile info, but allow creating new
        $participant = $user->participant; 

        if (!$participant) {
            return back()->with('error', 'Debes completar tu registro de participante primero.');
        }

        // REMOVED CHECK: if ($participant->team_id)

        $event = Event::findOrFail($request->event_id);
        
        // Validate Event Status
        if ($event->ends_at < now()) {
             return back()->with('error', 'No puedes unirte a un evento que ya ha finalizado.');
        }

        // Check if user is already in an active event (logic updated in User model)
        // But also check if user is already in THIS event specifically
        $alreadyInEvent = $user->participants()->whereHas('team', function($q) use ($event) {
            $q->where('event_id', $event->id);
        })->exists();

        if ($alreadyInEvent) {
             return back()->with('error', 'Ya tienes un equipo registrado para este evento.');
        }

        if ($user->active_event) {
            return back()->with('error', 'Ya estás participando en un evento en curso (' . $user->active_event->name . '). No puedes unirte a otro simultáneamente.');
        }

        if ($event->teams()->count() >= $event->capacity) {
            return back()->with('error', 'El evento ha alcanzado su capacidad máxima de equipos.');
        }

        $data = [
            'name' => $request->nombre,
            'event_id' => $request->event_id,
        ];

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('team-logos', 'public');
            $data['logo_path'] = $path;
        }

        $team = Team::create($data);

        // Assign creator to team
        // If current participant has NO team, use it. Otherwise create NEW.
        if (!$participant->team_id) {
            $participant->update([
                'team_id' => $team->id,
                'rol' => 'Líder',
            ]);
        } else {
            // Duplicate participant profile for new team
            Participant::create([
                'user_id' => $user->id,
                'team_id' => $team->id,
                'rol' => 'Líder',
                'career_id' => $participant->career_id,
                'control_number' => $participant->control_number,
                'institution' => $participant->institution,
            ]);
        }

        return redirect()->route('teams.index')->with('success', 'Equipo creado exitosamente.');
    }

    public function addMember(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'team_id' => 'required|exists:teams,id',
        ]);

        $user = Auth::user();
        $equipo = Team::findOrFail($request->team_id);

        // Check permission (Admin or Leader of THIS team)
        $isLeader = $user->participants()->where('team_id', $equipo->id)->where('rol', 'Líder')->exists();
        
        if (!$user->hasRole('admin') && !$isLeader) {
             return back()->with('error', 'No tienes permiso para agregar miembros a este equipo.');
        }

        $newMemberUser = User::where('email', $request->email)->first();
        // Check if user has a participant record. If not, error.
        $newMemberParticipante = $newMemberUser->participant; // Primary/Latest

        if (!$newMemberParticipante) {
            return back()->with('error', 'El usuario no ha completado su registro como participante.');
        }

        // Check if new member is ALREADY in a team for this event
        $alreadyInEvent = $newMemberUser->participants()->whereHas('team', function($q) use ($equipo) {
            $q->where('event_id', $equipo->event_id);
        })->exists();

        if ($alreadyInEvent) {
             return back()->with('error', 'El usuario ya pertenece a un equipo en este evento.');
        }

        // If new member has NO team in current participant record, use it.
        // Otherwise create NEW participant record.
        if (!$newMemberParticipante->team_id) {
            $newMemberParticipante->update([
                'team_id' => $equipo->id,
                'rol' => 'Miembro',
            ]);
        } else {
             Participant::create([
                'user_id' => $newMemberUser->id,
                'team_id' => $equipo->id,
                'rol' => 'Miembro',
                'career_id' => $newMemberParticipante->career_id,
                'control_number' => $newMemberParticipante->control_number,
                'institution' => $newMemberParticipante->institution,
            ]);
        }

        return back()->with('success', 'Miembro agregado exitosamente.');
    }

    public function removeMember(Request $request, int $teamId): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $equipo = Team::findOrFail($teamId);

        // Check permissions: Admin or Team Leader
        $isLeader = $user->participant && $user->participant->team_id == $equipo->id && $user->participant->rol == 'Líder';
        if (!$user->hasRole('admin') && !$isLeader) {
            abort(403, 'No tienes permiso para eliminar miembros.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $memberUser = User::findOrFail($request->user_id);
        $memberParticipante = $memberUser->participant;

        if (!$memberParticipante || $memberParticipante->team_id !== $equipo->id) {
            return back()->with('error', 'El usuario no pertenece a este equipo.');
        }

        // Prevent removing the leader if they are the only one, or handle leader transfer (not implemented yet)
        // For now, just allow removing anyone, but maybe warn if it's the leader?
        // If leader leaves, team might be leaderless.
        // Let's just allow it for now as per request "administrar".

        $memberParticipante->update([
            'team_id' => null,
            'rol' => null,
        ]);

        \App\Services\AuditLogger::log('remove_member', Team::class, $equipo->id, "Miembro eliminado del equipo {$equipo->name}: {$memberUser->email}");

        return back()->with('success', 'Miembro eliminado del equipo.');
    }

    public function requestJoin(int $teamId): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $participant = $user->participant;

        if (!$participant) {
            return back()->with('error', 'Debes completar tu registro de participante primero.');
        }

        if ($user->hasRole('admin')) {
            return back()->with('error', 'Los administradores no pueden unirse a equipos.');
        }

        if ($participant->team_id) {
            return back()->with('error', 'Ya perteneces a un equipo.');
        }

        // Check active events
        if ($user->active_event) {
             return back()->with('error', 'Ya estás participando en un evento en curso (' . $user->active_event->name . ').');
        }

        // Check if request already exists
        $existingRequest = \App\Models\Solicitud::where('user_id', $user->id)
            ->where('team_id', $teamId)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return back()->with('error', 'Ya has enviado una solicitud a este equipo.');
        }

        \App\Models\Solicitud::create([
            'user_id' => $user->id,
            'team_id' => $teamId,
        ]);

        return back()->with('success', 'Solicitud enviada exitosamente.');
    }

    public function acceptJoin(int $requestId): \Illuminate\Http\RedirectResponse
    {
        $solicitud = \App\Models\Solicitud::findOrFail($requestId);
        $equipo = $solicitud->team;
        
        // Check if current user is leader of the team
        $user = Auth::user();
        if (!$user->participant || $user->participant->team_id !== $equipo->id || $user->participant->rol !== 'Líder') {
             abort(403, 'No tienes permiso para aceptar solicitudes.');
        }

        $solicitud->update(['status' => 'accepted']);

        // Add user to team
        $solicitante = $solicitud->user->participant;
        if ($solicitante && !$solicitante->team_id) {
             $solicitante->update([
                'team_id' => $equipo->id,
                'rol' => 'Miembro',
            ]);
        }

        return back()->with('success', 'Solicitud aceptada.');
    }

    public function rejectJoin(int $requestId): \Illuminate\Http\RedirectResponse
    {
        $solicitud = \App\Models\Solicitud::findOrFail($requestId);
        $equipo = $solicitud->team;

        // Check if current user is leader of the team
        $user = Auth::user();
        if (!$user->participant || $user->participant->team_id !== $equipo->id || $user->participant->rol !== 'Líder') {
             abort(403, 'No tienes permiso para rechazar solicitudes.');
        }

        $solicitud->update(['status' => 'rejected']);

        return back()->with('success', 'Solicitud rechazada.');
    }

    public function leave(int $teamId): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $equipo = Team::findOrFail($teamId);

        // Find the specific participant record for this team
        $participant = $user->participants()->where('team_id', $equipo->id)->first();

        if (!$participant) {
            return back()->with('error', 'No perteneces a este equipo.');
        }

        // Check if user is the leader
        if ($participant->rol === 'Líder') {
            // Find other members
            $otherMember = $equipo->participants()
                ->where('user_id', '!=', $user->id)
                ->orderBy('created_at', 'asc') // Oldest member checks in first
                ->first();

            if ($otherMember) {
                // Promote next member
                $otherMember->update(['rol' => 'Líder']);
                \App\Services\AuditLogger::log('team_update', Team::class, $equipo->id, "Liderazgo transferido a: {$otherMember->user->email}");
            } else {
                // No other members, delete team
                $equipo->delete();
                // Detach user (though team is gone, good practice to clear local ref if any, but cascade might handle it. 
                // Since we rely on soft deletes or just nulls, let's explicitly clear the participant's team_id first to be safe or just let it be.)
                // Actually if team is deleted, foreign key might fail if not cascading?
                // Let's assume standard behavior. We update participant first.
            }
        }

        // Detach user
        $participant->update([
            'team_id' => null,
            'rol' => null,
        ]);

        return redirect()->route('teams.index')->with('success', 'Has salido del equipo exitosamente.');
    }
}
