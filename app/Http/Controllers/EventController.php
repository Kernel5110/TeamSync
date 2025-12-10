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
    public function index(): \Illuminate\View\View
    {
        // AÑADIDO: 'judges' para cargar la lista de jueces asignados a cada evento.
        $events = Event::with(['teams.participants.user', 'judges'])->paginate(9);
        $team = null;

        if (auth()->check() && auth()->user()->participant) {
            $team = auth()->user()->participant->team;
        }

        // Passing 'eventos' and 'equipo' to view to maintain compatibility with blade files
        return view('event', ['eventos' => $events, 'equipo' => $team]);
    }

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

        $event = Event::create($data);

        if ($request->has('categorias')) {
            $categoryIds = [];
            foreach ($request->categorias as $catName) {
                if ($catName) {
                    $category = \App\Models\Categoria::firstOrCreate(['name' => trim($catName)]);
                    $categoryIds[] = $category->id;
                }
            }
            $event->categories()->sync($categoryIds);
        }

        if ($request->has('criteria')) {
            $event->syncCriteria($request->criteria);
        }

        AuditLogger::log('create', Event::class, $event->id, "Evento creado: {$event->name}");

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
             // If array exists but is empty? Or if not present?
             // View usually sends it if inputs exist.
             // But existing logic detached if strictly has 'categorias'. 
             // Logic preserved but cleaned.
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

        $event = Event::findOrFail($id);
        $event->delete();

        AuditLogger::log('delete', Event::class, $id, "Evento eliminado: {$event->name}");

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
        foreach ($event->teams as $team) {
            foreach ($team->participants as $participant) {
                if ($participant->user && $participant->user->email) {
                    $emails->push($participant->user->email);
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
