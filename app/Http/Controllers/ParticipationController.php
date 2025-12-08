<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ParticipationController extends Controller
{
    public function show(int $evento_id): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $evento = Evento::findOrFail($evento_id);
        $user = Auth::user();

        // Check if user has a team in this event
        $equipo = Equipo::where('evento_id', $evento_id)
            ->whereHas('participantes', function($query) use ($user) {
                $query->where('usuario_id', $user->id);
            })->first();

        if (!$equipo) {
            return redirect()->route('events.index')->with('error', 'Debes registrar un equipo para participar en este evento.');
        }

        // Calculate Rank
        $rank = null;
        $eventTeams = $evento->equipos;
        
        // Only calculate if there are evaluations
        if ($eventTeams->count() > 0) {
            $ranking = $eventTeams->map(function ($e) {
                $evaluations = $e->evaluations;
                if ($evaluations->isEmpty()) return ['id' => $e->id, 'score' => 0];
                $score = $evaluations->sum(function ($eval) {
                    return $eval->score_innovation + $eval->score_social_impact + $eval->score_technical_viability;
                }) / $evaluations->count();
                return ['id' => $e->id, 'score' => $score];
            })->sortByDesc('score')->values();

            $foundRank = $ranking->search(function ($item) use ($equipo) {
                return $item['id'] == $equipo->id;
            });

            if ($foundRank !== false) {
                $rank = $foundRank + 1;
            }
        }

        $isEvaluated = $equipo->evaluations()->exists();

        return view('participation', compact('evento', 'equipo', 'rank', 'isEvaluated'));
    }

    public function upload(Request $request, int $evento_id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'technologies' => 'required|string',
            'github_repo' => 'required|url',
            'github_pages' => 'nullable|url',
            'project_description' => 'required|string',
            'evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB Max
        ]);

        $evento = Evento::findOrFail($evento_id);

        // Time Validation
        $startDateTime = $evento->fecha_inicio->copy()->setTimeFromTimeString($evento->start_time);
        $now = now('America/Mexico_City');
        
        if ($now->lessThan($startDateTime)) {
            return back()->with('error', 'El evento aún no ha comenzado. Inicia a las ' . $startDateTime->format('H:i'));
        }
        
        // Find the team of the current user for this event.
        $user = Auth::user();
        $equipo = Equipo::where('evento_id', $evento_id)
            ->whereHas('participantes', function($query) use ($user) {
                $query->where('usuario_id', $user->id); 
            })->first();

        if (!$equipo) {
             return back()->with('error', 'No eres parte de un equipo en este evento.');
        }

        if ($equipo->evaluations()->exists()) {
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

        $equipo->update($data);

        return back()->with('success', 'Información del proyecto actualizada exitosamente.');
    }
}
