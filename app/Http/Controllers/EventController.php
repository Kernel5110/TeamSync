<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Team;
use App\Models\Categoria;
use App\Models\Criterion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventReportMail;
use Illuminate\Support\Facades\Response;


use App\Services\AuditLogger;

class EventController extends Controller
{
    public function generatePdfReport(int $id): \Illuminate\Http\Response
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $evento = Event::with(['teams.participants.user', 'teams.evaluations'])->findOrFail($id);
        
        // Calculate ranking
        $ranking = $evento->teams->map(function ($team) {
            $team->total_score = $team->evaluations->avg('score');
            return $team;
        })->sortByDesc('total_score')->values();

        $pdf = Pdf::loadView('reports.event_summary', compact('evento', 'ranking'));
        
        return $pdf->download('Reporte_' . $evento->name . '.pdf');
    }

    public function generateCsvReport(int $id): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $evento = Event::with(['teams.participants.user', 'teams.evaluations'])->findOrFail($id);
        
        $csvFileName = 'Reporte_' . $evento->name . '.csv';
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

            foreach ($evento->teams as $team) {
                $score = $team->evaluations->avg('score') ?? 0;
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

        $evento = Event::with(['teams.participants.user', 'teams.evaluations'])->findOrFail($id);
        
        // Calculate ranking for PDF
        $ranking = $evento->teams->map(function ($team) {
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
        // AÑADIDO: 'judges' para cargar la lista de jueces asignados a cada evento.
        $eventos = Event::with(['teams.participants.user', 'judges'])->paginate(9);
        $equipo = null;

        if (auth()->check() && auth()->user()->participant) {
            $equipo = auth()->user()->participant->team;
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
            'nombre' => 'required|string|max:255', // View likely still sends 'nombre'
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'start_time' => 'required',
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'categorias' => 'nullable|array',
            'categorias.*' => 'nullable|string',
            'status_manual' => 'nullable|string|in:Próximo,En Curso,Finalizado',
        ]);

        // Map inputs to new schema
        $data = [
            'name' => $request->nombre,
            'description' => $request->descripcion,
            'location' => $request->ubicacion,
            'capacity' => $request->capacidad,
            'problem_statement' => $request->problem_statement,
            'status_manual' => $request->status_manual,
            'starts_at' => \Carbon\Carbon::parse($request->fecha_inicio . ' ' . $request->start_time),
            'ends_at' => \Carbon\Carbon::parse($request->fecha_fin)->endOfDay(),
        ];

        $evento = Event::create($data);

        if ($request->has('categorias')) {
            $categoryIds = [];
            foreach ($request->categorias as $catName) {
                if ($catName) {
                    $category = \App\Models\Categoria::firstOrCreate(['name' => trim($catName)]); // Categoria model might need update too? It was renamed to categories table.
                    $categoryIds[] = $category->id;
                }
            }
            $evento->categories()->sync($categoryIds); // Relationship name is categories()
        }

        if ($request->has('criteria')) {
            $evento->syncCriteria($request->criteria);
        }

        AuditLogger::log('create', Event::class, $evento->id, "Evento creado: {$evento->name}");

        return redirect()->route('events.index')->with('success', 'Evento creado correctamente.');
    }

    public function update(Request $request, $eventId): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->can('edit events')) {
            abort(403);
        }

        $event = Event::findOrFail($eventId);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'start_time' => 'required',
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'categorias' => 'nullable|array',
            'categorias.*' => 'nullable|string',
            'status_manual' => 'nullable|string|in:Próximo,En Curso,Finalizado',
        ]);

        $data = [
            'name' => $request->nombre,
            'description' => $request->descripcion,
            'location' => $request->ubicacion,
            'capacity' => $request->capacidad,
            'problem_statement' => $request->problem_statement,
            'status_manual' => $request->status_manual,
            'starts_at' => \Carbon\Carbon::parse($request->fecha_inicio . ' ' . $request->start_time),
            'ends_at' => \Carbon\Carbon::parse($request->fecha_fin)->endOfDay(),
        ];

        $event->update($data);

        if ($request->has('categorias')) {
            $categoryIds = [];
            foreach ($request->categorias as $catName) {
                if ($catName) {
                    $category = \App\Models\Categoria::firstOrCreate(['name' => trim($catName)]);
                    $categoryIds[] = $category->id;
                }
            }
            $event->categories()->sync($categoryIds);
        } else {
             if ($request->has('categorias')) {
                 $event->categories()->detach();
             }
        }

        if ($request->has('criteria')) {
            $event->syncCriteria($request->criteria);
        }

        AuditLogger::log('update', Event::class, $event->id, "Evento actualizado: {$event->name}");

        return redirect()->route('events.index')->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->can('delete events')) {
            abort(403);
        }

        $evento = Event::findOrFail($id);
        $evento->delete();



        AuditLogger::log('delete', Event::class, $id, "Evento eliminado: {$evento->name}");

        return redirect()->route('events.index')->with('success', 'Evento eliminado correctamente.');
    }

    public function sendAnnouncement(Request $request, $eventId): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $event = Event::with('teams.participants.user')->findOrFail($eventId);

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Collect all participant emails
        $emails = collect();
        foreach ($event->teams as $equipo) {
            foreach ($equipo->participants as $participante) {
                if ($participante->user && $participante->user->email) {
                    $emails->push($participante->user->email);
                }
            }
        }

        $emails = $emails->unique();

        if ($emails->isEmpty()) {
            return back()->with('error', 'No hay participantes con correo electrónico en este evento.');
        }

        // Send emails (queued ideally, but direct for now)
        foreach ($emails as $email) {
            Mail::to($email)->queue(new \App\Mail\EventAnnouncement($request->subject, $request->message, $event->name));
        }

        AuditLogger::log('announcement', Event::class, $event->id, "Anuncio enviado a {$emails->count()} participantes. Asunto: {$request->subject}");

        return back()->with('success', "Anuncio enviado correctamente a {$emails->count()} participantes.");
    }

    public function changeStatus(Request $request, $eventId): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $event = Event::findOrFail($eventId);

        $request->validate([
            'status_manual' => 'required|string|in:Próximo,En Curso,Finalizado',
        ]);

        $event->update(['status_manual' => $request->status_manual]);

        AuditLogger::log('status_change', Event::class, $event->id, "Estado cambiado a: {$request->status_manual}");

        return back()->with('success', 'Estado del evento actualizado correctamente.');
    }
}
