<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateMail;

class EvaluationController extends Controller
{
    // ... existing methods ...

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

        $rankText = 'Participante';
        if ($rank !== false && $rank < 3) {
             $rankText = ($rank + 1);
        }

        return view('certificate', compact('evento', 'equipo', 'rankText', 'rank'));
    }

    public function downloadCertificate($evento_id, $equipo_id)
    {
        $evento = Evento::with('equipos.evaluations')->findOrFail($evento_id);
        $equipo = \App\Models\Equipo::findOrFail($equipo_id);

        if ($equipo->evento_id !== $evento->id) {
            abort(404);
        }

        // Calculate ranking
        $ranking = $evento->equipos->map(function ($e) {
            $evaluations = $e->evaluations;
            if ($evaluations->isEmpty()) return ['id' => $e->id, 'score' => 0];
            $score = $evaluations->sum(function ($eval) {
                return $eval->score_innovation + $eval->score_social_impact + $eval->score_technical_viability;
            }) / $evaluations->count();
            return ['id' => $e->id, 'score' => $score];
        })->sortByDesc('score')->values();

        $rank = $ranking->search(function ($item) use ($equipo_id) {
            return $item['id'] == $equipo_id;
        });

        $rankText = 'Participante';
        if ($rank !== false && $rank < 3) {
             $rankText = ($rank + 1);
        }

        $pdf = Pdf::loadView('certificate', compact('evento', 'equipo', 'rankText', 'rank') + ['isPdf' => true])
            ->setPaper('letter', 'landscape');

        return $pdf->download('certificado-' . $equipo->nombre . '-' . $evento->nombre . '.pdf');
    }

    public function emailCertificate(Request $request, $evento_id, $equipo_id)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $evento = Evento::with('equipos.evaluations')->findOrFail($evento_id);
        $equipo = \App\Models\Equipo::findOrFail($equipo_id);

        if (!$evento->jueces->contains(auth()->user()->id)) {
            abort(403, 'Solo los jueces pueden enviar constancias.');
        }

        if ($equipo->evento_id !== $evento->id) {
            abort(404);
        }

        // Calculate ranking
        $ranking = $evento->equipos->map(function ($e) {
            $evaluations = $e->evaluations;
            if ($evaluations->isEmpty()) return ['id' => $e->id, 'score' => 0];
            $score = $evaluations->sum(function ($eval) {
                return $eval->score_innovation + $eval->score_social_impact + $eval->score_technical_viability;
            }) / $evaluations->count();
            return ['id' => $e->id, 'score' => $score];
        })->sortByDesc('score')->values();

        $rank = $ranking->search(function ($item) use ($equipo_id) {
            return $item['id'] == $equipo_id;
        });

        $rankText = 'Participante';
        if ($rank !== false && $rank < 3) {
             $rankText = ($rank + 1);
        }

        $pdf = Pdf::loadView('certificate', compact('evento', 'equipo', 'rankText', 'rank') + ['isPdf' => true])
            ->setPaper('letter', 'landscape');

        Mail::to($request->email)->send(new CertificateMail($pdf->output(), $equipo->nombre, $evento->nombre));

        return back()->with('success', 'Constancia enviada correctamente a ' . $request->email);
    }

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

        // Check if team has submitted project
        if (empty($equipo->project_name) || empty($equipo->github_repo)) {
            return redirect()->route('events.evaluate.show', $evento_id)->with('error', 'El equipo aún no ha subido la información de su proyecto.');
        }

        $evaluation = \App\Models\Evaluation::with('scores')->where('user_id', auth()->id())
            ->where('equipo_id', $equipo_id)
            ->where('evento_id', $evento_id)
            ->first();

        return view('evaluate_team', compact('evento', 'equipo', 'evaluation'));
    }

    public function store(Request $request, $evento_id)
    {
        $request->validate([
            'equipo_id' => 'required|exists:equipos,id',
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:0|max:10',
            'comments' => 'nullable|string',
            'private_notes' => 'nullable|string',
        ]);

        $evento = Evento::findOrFail($evento_id);

        if (!$evento->jueces->contains(auth()->user()->id)) {
            abort(403, 'No tienes permiso para evaluar este evento.');
        }

        $evaluation = \App\Models\Evaluation::where('user_id', auth()->id())
            ->where('evento_id', $evento_id)
            ->where('equipo_id', $request->equipo_id)
            ->first();

        if ($evaluation && $evaluation->finalized_at) {
            return back()->with('error', 'Esta evaluación ya ha sido finalizada y no se puede editar.');
        }

        $evaluation = \App\Models\Evaluation::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'evento_id' => $evento_id,
                'equipo_id' => $request->equipo_id,
            ],
            [
                'comments' => $request->comments,
                'private_notes' => $request->private_notes,
                // Legacy columns can be null or 0 if we are fully switching
                'score_innovation' => 0,
                'score_social_impact' => 0,
                'score_technical_viability' => 0,
                'is_conflict' => false, // Reset conflict if they are evaluating
            ]
        );

        // Save dynamic scores
        foreach ($request->scores as $criterionId => $score) {
            \App\Models\EvaluationScore::updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                    'criterion_id' => $criterionId,
                ],
                ['score' => $score]
            );
        }

        \App\Services\AuditLogger::log('evaluate', \App\Models\Evaluation::class, $evaluation->id, "Evaluación guardada para equipo ID: {$request->equipo_id} en evento ID: {$evento_id}");

        return redirect()->route('events.evaluate.show', $evento_id)->with('success', 'Evaluación guardada correctamente.');
    }

    public function ranking($id)
    {
        $evento = Evento::with(['equipos.evaluations.user', 'equipos.participantes.user'])->findOrFail($id);

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
                return $eval->scores->sum('score');
            });

            // Average score per judge
            $averageScore = $evaluations->count() > 0 ? $totalScore / $evaluations->count() : 0;

            return [
                'equipo' => $equipo,
                'average_score' => round($averageScore, 2),
                'evaluators_count' => $evaluations->count()
            ];
        })->sortByDesc('average_score')->values();

        $isJudge = $evento->jueces->contains(auth()->id());

        return view('ranking', compact('evento', 'ranking', 'isJudge'));
    }

    public function declareConflict($evento_id, $equipo_id)
    {
        $evento = Evento::findOrFail($evento_id);
        
        if (!$evento->jueces->contains(auth()->user()->id)) {
            abort(403, 'No tienes permiso para evaluar este evento.');
        }

        \App\Models\Evaluation::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'evento_id' => $evento_id,
                'equipo_id' => $equipo_id,
            ],
            [
                'is_conflict' => true,
                'comments' => 'Conflicto de interés declarado.',
            ]
        );

        return redirect()->route('events.evaluate.show', $evento_id)->with('success', 'Conflicto de interés declarado correctamente.');
    }

    public function finalize($evento_id, $equipo_id)
    {
        $evaluation = \App\Models\Evaluation::where('user_id', auth()->id())
            ->where('evento_id', $evento_id)
            ->where('equipo_id', $equipo_id)
            ->firstOrFail();

        $evaluation->update(['finalized_at' => now()]);

        return redirect()->route('events.evaluate.show', $evento_id)->with('success', 'Evaluación finalizada correctamente.');
    }

    public function unlock($id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Solo los administradores pueden desbloquear evaluaciones.');
        }

        $evaluation = \App\Models\Evaluation::findOrFail($id);
        $evaluation->update(['finalized_at' => null]);

        \App\Services\AuditLogger::log('unlock', \App\Models\Evaluation::class, $evaluation->id, "Evaluación desbloqueada por admin.");

        return back()->with('success', 'Evaluación desbloqueada correctamente.');
    }

    public function myFeedback()
    {
        $user = auth()->user();
        $participante = $user->participant;

        if (!$participante || !$participante->equipo) {
            return redirect()->route('start')->with('error', 'No tienes un equipo asignado.');
        }

        $equipo = $participante->equipo;
        $evento = $equipo->evento;

        // Only show feedback if event is finalized or evaluations are done (logic depends on requirements, assuming finalized evaluations are visible)
        // Or maybe only if event status is 'Finalizado'
        
        $evaluations = \App\Models\Evaluation::where('equipo_id', $equipo->id)
            ->whereNotNull('finalized_at')
            ->with(['scores.criterion'])
            ->get();

        return view('feedback', compact('equipo', 'evento', 'evaluations'));
    }
}
