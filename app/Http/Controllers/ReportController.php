<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventReportMail;
use Illuminate\Support\Facades\Response;
use App\Services\AuditLogger;

class ReportController extends Controller
{
    public function generatePdfReport(int $id): \Illuminate\Http\Response
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $event = Event::with(['teams.participants.user', 'teams.evaluations'])->findOrFail($id);
        
        // Calculate ranking using model method if available or dynamic logic
        // We really should use Event::getRanking() here too for consistency!
        // The original code calculated it manually. Let's upgrade it to use getRanking() 
        // effectively DRYing this too.
        
        $ranking = $event->getRanking();
        
        // Pass to view
        // Note: reports.event_summary view expects 'ranking' with 'total_score' property on team objects.
        // getRanking returns a collection of teams with 'total_score' set.
        // So this is compatible.

        $pdf = Pdf::loadView('reports.event_summary', ['evento' => $event, 'ranking' => $ranking]);
        
        return $pdf->download('Reporte_' . $event->name . '.pdf');
    }

    public function generateCsvReport(int $id): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $event = Event::with(['teams.participants.user', 'teams.evaluations.scores'])->findOrFail($id);
        
        $csvFileName = 'Reporte_' . $event->name . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Equipo', 'Proyecto', 'Tecnologias', 'Puntaje Promedio', 'Miembros', 'Progreso', 'Repositorio'];

        $callback = function() use($event, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $ranking = $event->getRanking(); // Use centralized logic for consistency

            foreach ($ranking as $team) {
                // getRanking sets total_score on the team object
                $score = $team->total_score ?? 0;
                $members = $team->participants->map(fn($p) => $p->user->name)->implode(', ');
                
                fputcsv($file, [
                    $team->name,
                    $team->project_name,
                    $team->technologies,
                    number_format($score, 2),
                    $members,
                    $team->progress . '%',
                    $team->github_repo
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function emailReport(int $id): \Illuminate\Http\RedirectResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $event = Event::with(['teams.participants.user', 'teams.evaluations'])->findOrFail($id);
        
        $ranking = $event->getRanking();

        $pdf = Pdf::loadView('reports.event_summary', ['evento' => $event, 'ranking' => $ranking]);

        Mail::to(Auth::user()->email)->send(new EventReportMail($event, $pdf));

        return back()->with('success', 'Reporte enviado a tu correo electrónico.');
    }
}
