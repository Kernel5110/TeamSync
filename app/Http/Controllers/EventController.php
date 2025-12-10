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
        
        return $pdf->download('Reporte_' . $evento->nombre . '.pdf');
    }

    public function generateCsvReport(int $id): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $evento = Event::with(['teams.participants.user', 'teams.evaluations'])->findOrFail($id);
        
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

            foreach ($evento->teams as $team) {
                $score = $team->evaluations->avg('score') ?? 0;
                $members = $team->participants->map(fn($p) => $p->user->name)->implode(', ');
                
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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'start_time' => 'required',
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'categorias' => 'nullable|array', // Changed to array
            'categorias.*' => 'nullable|string', // Allow nulls (empty input)
            'status_manual' => 'nullable|string|in:Próximo,En Curso,Finalizado',
        ]);

        $evento = Event::create($request->except('categorias'));

        if ($request->has('categorias')) {
            $categoryIds = [];
            foreach ($request->categorias as $catName) {
                if ($catName) {
                    $category = \App\Models\Categoria::firstOrCreate(['nombre' => trim($catName)]);
                    $categoryIds[] = $category->id;
                }
            }
            $evento->categorias()->sync($categoryIds);
        }

        if ($request->has('criteria')) {
            foreach ($request->criteria as $criterion) {
                if (!empty($criterion['name'])) {
                    $evento->criteria()->create([
                        'name' => $criterion['name'],
                        'max_score' => $criterion['max_score'] ?? 10,
                        'description' => $criterion['description'] ?? null,
                    ]);
                }
            }
        }



        AuditLogger::log('create', Event::class, $evento->id, "Evento creado: {$evento->nombre}");

        return redirect()->route('events.index')->with('success', 'Evento creado correctamente.');
    }

    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->can('edit events')) {
            abort(403);
        }

        $evento = Event::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'start_time' => 'required', // Added start_time
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'categorias' => 'nullable|array',
            'categorias.*' => 'nullable|string', // Allow nulls
            'status_manual' => 'nullable|string|in:Próximo,En Curso,Finalizado',
        ]);

        $evento->update($request->except('categorias'));

        if ($request->has('categorias')) {
            $categoryIds = [];
            foreach ($request->categorias as $catName) {
                if ($catName) {
                    $category = \App\Models\Categoria::firstOrCreate(['nombre' => trim($catName)]);
                    $categoryIds[] = $category->id;
                }
            }
            $evento->categorias()->sync($categoryIds);
        } else {
             // If no categories sent (e.g. all unchecked), detach all? 
             // Or maybe the UI sends empty array. 
             // If field is missing from request, it might mean "don't update" or "empty".
             // Usually checkboxes send nothing if unchecked. 
             // Let's assume if 'categorias' key exists (even empty), we sync.
             // But if it's missing, maybe we shouldn't touch it? 
             // Actually, for "edit", if we uncheck all, we want to clear.
             // So we should handle the case where it's not present if we use checkboxes.
             // But if we use a multi-select, it sends empty array or nothing.
             // Let's assume we always send 'categorias' if we want to update them.
             if ($request->has('categorias')) {
                 $evento->categorias()->detach(); // Clear if empty array passed
             }
        }

        // Sync Criteria
        // This is tricky because we might want to update existing ones or delete removed ones.
        // Simplest approach: Delete all and recreate? Or update existing if ID provided?
        // Let's go with: Delete all and recreate for simplicity in this MVP, 
        // BUT this would lose old scores if we delete criteria IDs that are linked to scores.
        // Better: Update existing, create new, delete missing.
        
        if ($request->has('criteria')) {
            $sentIds = [];
            foreach ($request->criteria as $criterion) {
                if (!empty($criterion['name'])) {
                    if (isset($criterion['id'])) {
                        $crit = \App\Models\Criterion::find($criterion['id']);
                        if ($crit && $crit->evento_id == $evento->id) {
                            $crit->update([
                                'name' => $criterion['name'],
                                'max_score' => $criterion['max_score'] ?? 10,
                                'description' => $criterion['description'] ?? null,
                            ]);
                            $sentIds[] = $crit->id;
                        }
                    } else {
                        $newCrit = $evento->criteria()->create([
                            'name' => $criterion['name'],
                            'max_score' => $criterion['max_score'] ?? 10,
                            'description' => $criterion['description'] ?? null,
                        ]);
                        $sentIds[] = $newCrit->id;
                    }
                }
            }
            // Delete criteria not in request
            $evento->criteria()->whereNotIn('id', $sentIds)->delete();
        }



        AuditLogger::log('update', Event::class, $evento->id, "Evento actualizado: {$evento->nombre}");

        return redirect()->route('events.index')->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->can('delete events')) {
            abort(403);
        }

        $evento = Event::findOrFail($id);
        $evento->delete();



        AuditLogger::log('delete', Event::class, $id, "Evento eliminado: {$evento->nombre}");

        return redirect()->route('events.index')->with('success', 'Evento eliminado correctamente.');
    }

    public function sendAnnouncement(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $evento = Event::with('teams.participants.user')->findOrFail($id);

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Collect all participant emails
        $emails = collect();
        foreach ($evento->teams as $equipo) {
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
            Mail::to($email)->queue(new \App\Mail\EventAnnouncement($request->subject, $request->message, $evento->nombre));
        }

        AuditLogger::log('announcement', Event::class, $evento->id, "Anuncio enviado a {$emails->count()} participantes. Asunto: {$request->subject}");

        return back()->with('success', "Anuncio enviado correctamente a {$emails->count()} participantes.");
    }

    public function changeStatus(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $evento = Event::findOrFail($id);

        $request->validate([
            'status_manual' => 'required|string|in:Próximo,En Curso,Finalizado',
        ]);

        $evento->update(['status_manual' => $request->status_manual]);

        AuditLogger::log('status_change', Event::class, $evento->id, "Estado cambiado a: {$request->status_manual}");

        return back()->with('success', 'Estado del evento actualizado correctamente.');
    }
}
