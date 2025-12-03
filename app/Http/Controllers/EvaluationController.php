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

        return view('evaluate', compact('evento'));
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

        \App\Models\Evaluation::create([
            'user_id' => auth()->id(),
            'evento_id' => $evento_id,
            'equipo_id' => $request->equipo_id,
            'score_innovation' => $request->score_innovation,
            'score_social_impact' => $request->score_social_impact,
            'score_technical_viability' => $request->score_technical_viability,
            'comments' => $request->comments,
        ]);

        return back()->with('success', 'Evaluación enviada correctamente.');
    }
}
