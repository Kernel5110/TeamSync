<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Event;
use App\Models\Team;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateMail;

class EvaluationController extends Controller
{
    // ... existing methods ...

    public function certificate($eventId, $teamId)
    {
        $evento = Event::with('teams.evaluations')->findOrFail($eventId);
        $equipo = \App\Models\Team::findOrFail($teamId);

        if ($equipo->event_id !== $evento->id) {
            abort(404);
        }

        // Calculate ranking to verify position
        $ranking = $evento->getRanking();

        // Find rank
        $rank = $ranking->search(function ($item) use ($teamId) {
            return $item->id == $teamId;
        });

        $rankText = 'Participante';
        if ($rank !== false && $rank < 3) {
             $rankText = ($rank + 1);
        }

        return view('certificate', compact('evento', 'equipo', 'rankText', 'rank'));
    }

    public function downloadCertificate($eventId, $teamId)
    {
        $evento = Event::with('teams.evaluations')->findOrFail($eventId);
        $equipo = \App\Models\Team::findOrFail($teamId);

        if ($equipo->event_id !== $evento->id) {
            abort(404);
        }

        // Calculate ranking
        $ranking = $evento->getRanking();

        $rank = $ranking->search(function ($item) use ($teamId) {
            return $item->id == $teamId;
        });

        $rankText = 'Participante';
        if ($rank !== false && $rank < 3) {
             $rankText = ($rank + 1);
        }

        $pdf = Pdf::loadView('certificate', compact('evento', 'equipo', 'rankText', 'rank') + ['isPdf' => true])
            ->setPaper('letter', 'landscape');

        return $pdf->download('certificado-' . $equipo->name . '-' . $evento->name . '.pdf');
    }

    public function emailCertificate(Request $request, $eventId, $teamId)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $evento = Event::with('teams.evaluations')->findOrFail($eventId);
        $equipo = \App\Models\Team::findOrFail($teamId);

        if (!$evento->judges->contains(auth()->user()->id)) {
            abort(403, 'Solo los jueces pueden enviar constancias.');
        }

        if ($equipo->event_id !== $evento->id) {
            abort(404);
        }

        // Calculate ranking
        $ranking = $evento->getRanking();

        $rank = $ranking->search(function ($item) use ($teamId) {
            return $item->id == $teamId;
        });

        $rankText = 'Participante';
        if ($rank !== false && $rank < 3) {
             $rankText = ($rank + 1);
        }

        $pdf = Pdf::loadView('certificate', compact('evento', 'equipo', 'rankText', 'rank') + ['isPdf' => true])
            ->setPaper('letter', 'landscape');

        Mail::to($request->email)->send(new CertificateMail($pdf->output(), $equipo->name, $evento->name));

        return back()->with('success', 'Constancia enviada correctamente a ' . $request->email);
    }

    public function show($eventId)
    {
        $evento = Event::with('teams.participants')->findOrFail($eventId);
        
        // Check if user is assigned as judge for this event
        if (!$evento->judges->contains(auth()->user()->id)) {
            abort(403, 'No tienes permiso para evaluar este evento.');
        }

        if ($evento->status === 'Finalizado') {
             return redirect()->route('events.index')->with('error', 'El evento ha finalizado y ya no se puede evaluar.');
        }

        // Get IDs of teams already evaluated by this user
        $evaluatedTeams = \App\Models\Evaluation::where('user_id', auth()->id())
            ->where('event_id', $eventId)
            ->pluck('team_id')
            ->toArray();

        return view('evaluate', compact('evento', 'evaluatedTeams'));
    }

    public function evaluateTeam($eventId, $teamId)
    {
        $evento = Event::findOrFail($eventId);
        $equipo = \App\Models\Team::findOrFail($teamId);

        if (!$evento->judges->contains(auth()->user()->id)) {
            abort(403, 'No tienes permiso para evaluar este evento.');
        }

        if ($evento->status === 'Finalizado') {
            return redirect()->route('events.index')->with('error', 'El evento ha finalizado.');
        }

        if ($equipo->event_id !== $evento->id) {
            abort(404, 'El equipo no pertenece a este evento.');
        }

        // Check if team has submitted project
        if (empty($equipo->project_name) || empty($equipo->github_repo)) {
            return redirect()->route('events.evaluate.show', $eventId)->with('error', 'El equipo aún no ha subido la información de su proyecto.');
        }

        $evaluation = \App\Models\Evaluation::with('scores')->where('user_id', auth()->id())
            ->where('team_id', $teamId)
            ->where('event_id', $eventId)
            ->first();

        return view('evaluate_team', compact('evento', 'equipo', 'evaluation'));
    }

    public function store(Request $request, $eventId)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:0',
            'comments' => 'nullable|string',
            'private_notes' => 'nullable|string',
        ]);

        $evento = Event::findOrFail($eventId);

        if (!$evento->judges->contains(auth()->user()->id)) {
            abort(403, 'No tienes permiso para evaluar este evento.');
        }

        if ($evento->status === 'Finalizado') {
            abort(403, 'No se pueden guardar evaluaciones de un evento finalizado.');
        }

        $evaluation = \App\Models\Evaluation::where('user_id', auth()->id())
            ->where('event_id', $eventId)
            ->where('team_id', $request->team_id)
            ->first();

        if ($evaluation && $evaluation->finalized_at) {
            return back()->with('error', 'Esta evaluación ya ha sido finalizada y no se puede editar.');
        }

        $evaluation = \App\Models\Evaluation::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'event_id' => $eventId,
                'team_id' => $request->team_id,
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

        \App\Services\AuditLogger::log('evaluate', \App\Models\Evaluation::class, $evaluation->id, "Evaluación guardada para equipo ID: {$request->team_id} en evento ID: {$eventId}");

        return redirect()->route('events.evaluate.show', $eventId)->with('success', 'Evaluación guardada correctamente.');
    }

    public function ranking($eventId)
    {
        $evento = Event::with(['teams.evaluations.user', 'teams.participants.user'])->findOrFail($eventId);

        // Use centralized ranking logic
        $teams = $evento->getRanking();

        $ranking = $teams->map(function ($equipo) {
            return [
                'equipo' => $equipo,
                'average_score' => round($equipo->total_score, 2),
                'evaluators_count' => $equipo->evaluations->count()
            ];
        }); // getRanking already sorts by score desc

        $isJudge = $evento->judges->contains(auth()->id());

        return view('ranking', compact('evento', 'ranking', 'isJudge'));
    }



    public function declareConflict($eventId, $teamId)
    {
        $evento = Event::findOrFail($eventId);
        
        if (!$evento->judges->contains(auth()->user()->id)) {
            abort(403, 'No tienes permiso para evaluar este evento.');
        }

        \App\Models\Evaluation::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'event_id' => $eventId,
                'team_id' => $teamId,
            ],
            [
                'is_conflict' => true,
                'comments' => 'Conflicto de interés declarado.',
            ]
        );

        return redirect()->route('events.evaluate.show', $eventId)->with('success', 'Conflicto de interés declarado correctamente.');
    }

    public function finalize($eventId, $teamId)
    {
        $evaluation = \App\Models\Evaluation::where('user_id', auth()->id())
            ->where('event_id', $eventId)
            ->where('team_id', $teamId)
            ->firstOrFail();

        $evaluation->update(['finalized_at' => now()]);

        return redirect()->route('events.evaluate.show', $eventId)->with('success', 'Evaluación finalizada correctamente.');
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

        if (!$participante || !$participante->team) {
            return redirect()->route('start')->with('error', 'No tienes un equipo asignado.');
        }

        $equipo = $participante->team;
        $evento = $equipo->event;

        // Only show feedback if event is finalized or evaluations are done (logic depends on requirements, assuming finalized evaluations are visible)
        // Or maybe only if event status is 'Finalizado'
        
        $evaluations = \App\Models\Evaluation::where('team_id', $equipo->id)
            ->whereNotNull('finalized_at')
            ->with(['scores.criterion'])
            ->get();

        return view('feedback', compact('equipo', 'evento', 'evaluations'));
    }
}
