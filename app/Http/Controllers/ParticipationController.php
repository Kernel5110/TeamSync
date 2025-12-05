<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ParticipationController extends Controller
{
    public function show($evento_id)
    {
        $evento = Evento::findOrFail($evento_id);
        $user = Auth::user();

        // Check if user has a team in this event
        $equipo = Equipo::where('evento_id', $evento_id)
            ->whereHas('participantes', function($query) use ($user) {
                $query->where('usuario_id', $user->id);
            })->first();

        if (!$equipo) {
            return redirect()->route('event')->with('error', 'Debes registrar un equipo para participar en este evento.');
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

            if ($foundRank !== false) {
                $rank = $foundRank + 1;
            }
        }

        $isEvaluated = $equipo->evaluations()->exists();

        return view('participation', compact('evento', 'equipo', 'rank', 'isEvaluated'));
    }

    public function upload(Request $request, $evento_id)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'technologies' => 'required|string',
            'github_repo' => 'required|url',
            'github_pages' => 'nullable|url',
            'project_description' => 'required|string',
        ]);

        $evento = Evento::findOrFail($evento_id);
        
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

        $equipo->update([
            'project_name' => $request->project_name,
            'technologies' => $request->technologies,
            'github_repo' => $request->github_repo,
            'github_pages' => $request->github_pages,
            'project_description' => $request->project_description,
        ]);

        return back()->with('success', 'Información del proyecto actualizada exitosamente.');
    }
}
