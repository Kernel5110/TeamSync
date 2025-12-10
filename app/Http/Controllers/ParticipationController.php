<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Team;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ParticipationController extends Controller
{
    public function show(int $eventId): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $event = Event::findOrFail($eventId);
        $user = Auth::user();

        // Check if user has a team in this event
        $team = Team::where('event_id', $eventId)
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->first();

        if (!$team) {
            return redirect()->route('events.index')->with('error', 'Debes registrar un equipo para participar en este evento.');
        }

        // Calculate Rank
        $rank = null;
        $ranking = $event->getRanking();
        
        $foundRank = $ranking->search(function ($item) use ($team) {
            return $item->id == $team->id;
        });

        if ($foundRank !== false) {
            $rank = $foundRank + 1;
        }

        $isEvaluated = $team->evaluations()->exists();

        // Passing 'evento' and 'equipo' to view to avoid breaking blade templates for now
        return view('participation', [
            'evento' => $event,
            'equipo' => $team,
            'rank' => $rank,
            'isEvaluated' => $isEvaluated
        ]);
    }

    public function upload(Request $request, int $eventId): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'technologies' => 'required|string',
            'github_repo' => 'required|url',
            'github_pages' => 'nullable|url',
            'project_description' => 'required|string',
            'evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB Max
        ]);

        $event = Event::findOrFail($eventId);

        // Time Validation
        $startDateTime = $event->starts_at;
        $now = now('America/Mexico_City');
        
        if ($now->lessThan($startDateTime)) {
            return back()->with('error', 'El evento aún no ha comenzado. Inicia a las ' . $startDateTime->format('H:i'));
        }
        
        // Find the team of the current user for this event.
        $user = Auth::user();
        $team = Team::where('event_id', $eventId)
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id); 
            })->first();

        if (!$team) {
             return back()->with('error', 'No eres parte de un equipo en este evento.');
        }

        if ($team->evaluations()->exists()) {
            return back()->with('error', 'No puedes editar el proyecto porque ya ha sido evaluado.');
        }

        $data = [
            'project_name' => $request->project_name,
            'technologies' => $request->technologies,
            'github_repo' => $request->github_repo,
            'github_pages' => $request->github_pages,
            'project_description' => $request->project_description,
        ];

        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('evidence', 'public');
            $data['evidence_path'] = $path;
        }

        $team->update($data);

        return back()->with('success', 'Información del proyecto actualizada exitosamente.');
    }
}
