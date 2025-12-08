<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventReportMail;
use Illuminate\Support\Facades\Response;

class EventController extends Controller
{
    public function generatePdfReport(int $id): \Illuminate\Http\Response
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $evento = Evento::with(['equipos.participantes.user', 'equipos.evaluations'])->findOrFail($id);
        
        // Calculate ranking
        $ranking = $evento->equipos->map(function ($team) {
            $team->total_score = $team->evaluations->avg('score');
            return $team;
        })->sortByDesc('total_score')->values();

        $pdf = Pdf::loadView('reports.event_summary', compact('evento', 'ranking'));
        
        return $pdf->download('Reporte_' . $evento->nombre . '.pdf');
    }

    public function generateCsvReport(int $id): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $evento = Evento::with(['equipos.participantes.user', 'equipos.evaluations'])->findOrFail($id);
        
        $csvFileName = 'Reporte_' . $evento->nombre . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Equipo', 'Proyecto', 'Tecnologias', 'Puntaje Promedio', 'Miembros', 'Progreso', 'Repositorio'];

        $callback = function() use($evento, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($evento->equipos as $team) {
                $score = $team->evaluations->avg('score') ?? 0;
                $members = $team->participantes->map(fn($p) => $p->user->name)->implode(', ');
                
                fputcsv($file, [
                    $team->nombre,
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

        $evento = Evento::with(['equipos.participantes.user', 'equipos.evaluations'])->findOrFail($id);
        
        // Calculate ranking for PDF
        $ranking = $evento->equipos->map(function ($team) {
            $team->total_score = $team->evaluations->avg('score');
            return $team;
        })->sortByDesc('total_score')->values();

        $pdf = Pdf::loadView('reports.event_summary', compact('evento', 'ranking'));

        Mail::to(Auth::user()->email)->send(new EventReportMail($evento, $pdf));

        return back()->with('success', 'Reporte enviado a tu correo electrónico.');
    }

    /**
     * Mostrar la página de eventos
     */
   // En EventController.php
    /**
     * Mostrar la página de eventos
     */
    public function index(): \Illuminate\View\View
    {
        // AÑADIDO: 'jueces' para cargar la lista de jueces asignados a cada evento.
        $eventos = Evento::with(['equipos.participantes.user', 'jueces'])->paginate(9);
        $equipo = null;

        if (auth()->check() && auth()->user()->participant) {
            $equipo = auth()->user()->participant->equipo;
        }

        return view('event', compact('eventos', 'equipo'));
    }
// ... resto del controller

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->can('create events')) {
            abort(403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'start_time' => 'required',
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'categoria' => 'nullable|string|max:255',
        ]);

        Evento::create($request->all());

        return redirect()->route('events.index')->with('success', 'Evento creado correctamente.');
    }

    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->can('edit events')) {
            abort(403);
        }

        $evento = Evento::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'categoria' => 'nullable|string|max:255',
        ]);

        $evento->update($request->all());

        return redirect()->route('events.index')->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->can('delete events')) {
            abort(403);
        }

        $evento = Evento::findOrFail($id);
        $evento->delete();

        return redirect()->route('events.index')->with('success', 'Evento eliminado correctamente.');
    }
}
