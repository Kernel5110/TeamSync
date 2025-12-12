<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $user = Auth::user();
        $participants = $user->participants; 
        $myTeams = $participants->map(function ($p) {
            return $p->team;
        })->filter();

        $equipo = $myTeams->first(); 

        $eventos = Event::where('ends_at', '>=', now())
                        ->get()
                        ->filter(function($event) {
                            return $event->status !== 'Finalizado';
                        });
        
        $allTeams = null;
        if ($user->hasRole('admin')) {
            $allTeams = Team::with(['participants.user', 'event'])->paginate(10, ['*'], 'all_teams_page');
        }

        $myTeamIds = $myTeams->pluck('id')->toArray();

        $otherTeamsQuery = Team::with(['event', 'participants.user']);
        if (!empty($myTeamIds)) {
            $otherTeamsQuery->whereNotIn('id', $myTeamIds);
        }
        $otherTeams = $otherTeamsQuery->paginate(10, ['*'], 'other_teams_page');

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

        $myPendingRequest = \App\Models\Solicitud::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        $myInvitations = \App\Models\Solicitud::where('user_id', $user->id)
            ->where('status', 'invited')
            ->with(['team.event'])
            ->get();

        return view('team', compact('equipo', 'myTeams', 'eventos', 'allTeams', 'otherTeams', 'pendingRequests', 'myPendingRequest', 'myInvitations'));
    }

    public function search(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\View\View
    {
        $query = $request->input('query');
        $teams = [];
        
        if ($query) {
            $teams = Team::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhereHas('event', function($fq) use ($query) {
                      $fq->where('name', 'LIKE', "%{$query}%");
                  });
            })
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
        
        foreach ($equipo->participants as $participante) {
            $participante->update([
                'team_id' => null,
                'rol' => null
            ]);
        }

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
        ]);

        $user = Auth::user();
        $participant = $user->participant; 

        if (!$participant) {
            return back()->with('error', 'Debes completar tu registro de participante primero.');
        }


        $event = Event::findOrFail($request->event_id);
        
        if ($event->ends_at < now() || $event->status === 'Finalizado') {
             return back()->with('error', 'No puedes unirte a un evento que ya ha finalizado.');
        }

        $alreadyInEvent = $user->participants()->whereHas('team', function($q) use ($event) {
            $q->where('event_id', $event->id);
        })->exists();

        if ($alreadyInEvent) {
             return back()->with('error', 'Ya tienes un equipo registrado para este evento.');
        }

        if ($event->teams()->count() >= $event->capacity) {
            return back()->with('error', 'El evento ha alcanzado su capacidad máxima de equipos.');
        }

        $data = [
            'name' => $request->nombre,
            'event_id' => $request->event_id,
        ];

        $team = Team::create($data);

        if (!$participant->team_id) {
            $participant->update([
                'team_id' => $team->id,
                'rol' => 'Líder',
            ]);
        } else {
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

        $isLeader = $user->participants()->where('team_id', $equipo->id)->where('rol', 'Líder')->exists();
        
        if (!$user->hasRole('admin') && !$isLeader) {
             return back()->with('error', 'No tienes permiso para agregar miembros a este equipo.');
        }

        $newMemberUser = User::where('email', $request->email)->first();
        $newMemberParticipante = $newMemberUser->participant; 

        if (!$newMemberParticipante) {
            return back()->with('error', 'El usuario no ha completado su registro como participante.');
        }

        $alreadyInEvent = $newMemberUser->participants()->whereHas('team', function($q) use ($equipo) {
            $q->where('event_id', $equipo->event_id);
        })->exists();

        if ($alreadyInEvent) {
             return back()->with('error', 'El usuario ya pertenece a un equipo en este evento.');
        }

        $existingInvitation = \App\Models\Solicitud::where('user_id', $newMemberUser->id)
            ->where('team_id', $equipo->id)
            ->where('status', 'invited')
            ->exists();

        if ($existingInvitation) {
            return back()->with('error', 'Ya has enviado una invitación a este usuario.');
        }

        \App\Models\Solicitud::create([
            'user_id' => $newMemberUser->id,
            'team_id' => $equipo->id,
            'status' => 'invited'
        ]);

        return back()->with('success', 'Invitación enviada exitosamente.');
    }

    public function removeMember(Request $request, int $teamId): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $equipo = Team::findOrFail($teamId);

        $isLeader = $user->participants()
                         ->where('team_id', $equipo->id)
                         ->where('rol', 'Líder')
                         ->exists();

        if (!$user->hasRole('admin') && !$isLeader) {
            abort(403, 'No tienes permiso para eliminar miembros.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $memberUser = User::findOrFail($request->user_id);
        
        $memberParticipante = $memberUser->participants()
                                         ->where('team_id', $equipo->id)
                                         ->first();

        if (!$memberParticipante) {
            return back()->with('error', 'El usuario no pertenece a este equipo.');
        }

        $memberParticipante->update([
            'team_id' => null,
            'rol' => null,
        ]);

        \App\Services\AuditLogger::log('remove_member', Team::class, $equipo->id, "Miembro eliminado del equipo {$equipo->name}: {$memberUser->email}");

        if ($equipo->participants()->count() === 0) {
            $equipo->delete();
            \App\Services\AuditLogger::log('delete', Team::class, $equipo->id, "Equipo eliminado automáticamente por falta de miembros: {$equipo->name}");
            return redirect()->route('teams.index')->with('success', 'Miembro eliminado. El equipo ha sido eliminado porque no quedaron participantes.');
        }

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

        $alreadyInEvent = $user->participants()->whereHas('team', function($q) use ($teamId) {
            $team = Team::find($teamId);
            if ($team) {
                $q->where('event_id', $team->event_id);
            }
        })->exists();

        if ($alreadyInEvent) {
             return back()->with('error', 'Ya tienes un equipo registrado para este evento.');
        }

        $team = Team::findOrFail($teamId);
        if ($team->event && $team->event->status === 'Finalizado') {
             return back()->with('error', 'No puedes unirte a un equipo de un evento finalizado.');
        }

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
        
        $user = Auth::user();
        if (!$user->participant || $user->participant->team_id !== $equipo->id || $user->participant->rol !== 'Líder') {
             abort(403, 'No tienes permiso para aceptar solicitudes.');
        }

        $solicitud->update(['status' => 'accepted']);

        $solicitante = $solicitud->user->participant;
        
        if ($solicitante && !$solicitante->team_id) {
             $solicitante->update([
                'team_id' => $equipo->id,
                'rol' => 'Miembro',
            ]);
        } else {
             Participant::create([
                'user_id' => $solicitud->user->id,
                'team_id' => $equipo->id,
                'rol' => 'Miembro',
                'career_id' => $solicitante ? $solicitante->career_id : null,
                'control_number' => $solicitante ? $solicitante->control_number : null,
                'institution' => $solicitante ? $solicitante->institution : null,
            ]);
        }

        return back()->with('success', 'Solicitud aceptada.');
    }

    public function rejectJoin(int $requestId): \Illuminate\Http\RedirectResponse
    {
        $solicitud = \App\Models\Solicitud::findOrFail($requestId);
        $equipo = $solicitud->team;

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

        $participant = $user->participants()->where('team_id', $equipo->id)->first();

        if (!$participant) {
            return back()->with('error', 'No perteneces a este equipo.');
        }

        if ($participant->rol === 'Líder') {
            $otherMember = $equipo->participants()
                ->where('user_id', '!=', $user->id)
                ->orderBy('created_at', 'asc') 
                ->first();

            if ($otherMember) {
                $otherMember->update(['rol' => 'Líder']);
                \App\Services\AuditLogger::log('team_update', Team::class, $equipo->id, "Liderazgo transferido a: {$otherMember->user->email}");
            } else {
                $equipo->delete();
            }
        }

        $participant->update([
            'team_id' => null,
            'rol' => null,
        ]);

        if ($equipo->participants()->count() === 0) {
            $equipo->delete();
            \App\Services\AuditLogger::log('delete', Team::class, $equipo->id, "Equipo eliminado automáticamente por falta de miembros: {$equipo->name}");
        }

        return redirect()->route('teams.index')->with('success', 'Has salido del equipo exitosamente.');
    }
    public function acceptInvitation(int $requestId): \Illuminate\Http\RedirectResponse
    {
        $solicitud = \App\Models\Solicitud::findOrFail($requestId);
        
        if ($solicitud->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
        $equipo = $solicitud->team;
        $participant = $user->participants()->where('team_id', $equipo->id)->first();
        if ($participant) return back()->with('error', 'Ya estás en el equipo.');

        $alreadyInEvent = $user->participants()->whereHas('team', function($q) use ($equipo) {
            $q->where('event_id', $equipo->event_id);
        })->exists();

        if ($alreadyInEvent) {
             return back()->with('error', 'Ya perteneces a un equipo en este evento.');
        }

        $solicitud->update(['status' => 'accepted']);
        
        $baseParticipant = $user->participant; 
        
        if ($baseParticipant && !$baseParticipant->team_id) {
             $baseParticipant->update([
                'team_id' => $equipo->id,
                'rol' => 'Miembro',
            ]);
        } else {
             Participant::create([
                'user_id' => $user->id,
                'team_id' => $equipo->id,
                'rol' => 'Miembro',
                'career_id' => $baseParticipant ? $baseParticipant->career_id : null,
                'control_number' => $baseParticipant ? $baseParticipant->control_number : null,
                'institution' => $baseParticipant ? $baseParticipant->institution : null,
            ]);
        }

        return back()->with('success', 'Has aceptado la invitación al equipo.');
    }

    public function rejectInvitation(int $requestId): \Illuminate\Http\RedirectResponse
    {
        $solicitud = \App\Models\Solicitud::findOrFail($requestId);
        if ($solicitud->user_id !== Auth::id()) {
            abort(403);
        }
        $solicitud->update(['status' => 'rejected']);
        return back()->with('success', 'Has rechazado la invitación.');
    }
}
