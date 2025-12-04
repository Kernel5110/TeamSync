<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;

class EvaluationController extends Controller
{
    public function show($id)
    {
        $evento = Evento::with('equipos.participantes')->findOrFail($id);
        
        // Check if user is assigned as judge for this event
        if (!$evento->jueces->contains(auth()->user()->id)) {
            abort(403, 'No tienes permiso para evaluar este evento.');
        }

        // Get IDs of teams already evaluated by this user
        $evaluatedTeams = \App\Models\Evaluation::where('user_id', auth()->id())
            ->where('evento_id', $id)
            ->pluck('equipo_id')
            ->toArray();

        return view('evaluate', compact('evento', 'evaluatedTeams'));
    }

    public function evaluateTeam($evento_id, $equipo_id)
    {
        $evento = Evento::findOrFail($evento_id);
        $equipo = \App\Models\Equipo::findOrFail($equipo_id);

        if (!$evento->jueces->contains(auth()->user()->id)) {
            abort(403, 'No tienes permiso para evaluar este evento.');
        }

        if ($equipo->evento_id !== $evento->id) {
            abort(404, 'El equipo no pertenece a este evento.');
        }

        $evaluation = \App\Models\Evaluation::where('user_id', auth()->id())
            ->where('equipo_id', $equipo_id)
            ->where('evento_id', $evento_id)
            ->first();

        return view('evaluate_team', compact('evento', 'equipo', 'evaluation'));
    }

    public function store(Request $request, $evento_id)
    {
        $request->validate([
            'equipo_id' => 'required|exists:equipos,id',
            'score_innovation' => 'required|integer|min:0|max:10',
            'score_social_impact' => 'required|integer|min:0|max:10',
            'score_technical_viability' => 'required|integer|min:0|max:10',
            'comments' => 'nullable|string',
        ]);

        $evento = Evento::findOrFail($evento_id);

        if (!$evento->jueces->contains(auth()->user()->id)) {
            abort(403, 'No tienes permiso para evaluar este evento.');
        }

        // Check if already evaluated? Optional.
        // For now, allow multiple or update existing? Let's assume create new.

        \App\Models\Evaluation::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'evento_id' => $evento_id,
                'equipo_id' => $request->equipo_id,
            ],
            [
                'score_innovation' => $request->score_innovation,
                'score_social_impact' => $request->score_social_impact,
                'score_technical_viability' => $request->score_technical_viability,
                'comments' => $request->comments,
            ]
        );


        return redirect()->route('event.evaluate', $evento_id)->with('success', 'Evaluación guardada correctamente.');
    }

    public function ranking($id)
    {
        $evento = Evento::with(['equipos.evaluations', 'equipos.participantes.user'])->findOrFail($id);

        $ranking = $evento->equipos->map(function ($equipo) {
            $evaluations = $equipo->evaluations;
            
            if ($evaluations->isEmpty()) {
                return [
                    'equipo' => $equipo,
                    'average_score' => 0,
                    'evaluators_count' => 0
                ];
            }

            $totalScore = $evaluations->sum(function ($eval) {
                return $eval->score_innovation + $eval->score_social_impact + $eval->score_technical_viability;
            });

            // Max score per judge is 30.
            // We can calculate average score out of 30, or normalized to 100, or just average points.
            // Let's use average total points per judge (0-30).
            $averageScore = $totalScore / $evaluations->count();

            return [
                'equipo' => $equipo,
                'average_score' => round($averageScore, 2),
                'evaluators_count' => $evaluations->count()
            ];
        })->sortByDesc('average_score')->values();


        return view('ranking', compact('evento', 'ranking'));
    }

    public function certificate($evento_id, $equipo_id)
    {
        $evento = Evento::with('equipos.evaluations')->findOrFail($evento_id);
        $equipo = \App\Models\Equipo::findOrFail($equipo_id);

        if ($equipo->evento_id !== $evento->id) {
            abort(404);
        }

        // Calculate ranking to verify position
        $ranking = $evento->equipos->map(function ($e) {
            $evaluations = $e->evaluations;
            if ($evaluations->isEmpty()) return ['id' => $e->id, 'score' => 0];
            $score = $evaluations->sum(function ($eval) {
                return $eval->score_innovation + $eval->score_social_impact + $eval->score_technical_viability;
            }) / $evaluations->count();
            return ['id' => $e->id, 'score' => $score];
        })->sortByDesc('score')->values();

        // Find rank
        $rank = $ranking->search(function ($item) use ($equipo_id) {
            return $item['id'] == $equipo_id;
        });

        if ($rank === false) {
            abort(404);
        }

        $rank += 1; // 0-indexed to 1-indexed

        return view('certificate', compact('evento', 'equipo', 'rank'));
    }
}
