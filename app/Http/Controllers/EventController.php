<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Mostrar la página de eventos
     */
    public function index()
    {
        $eventos = Evento::all();
        $equipo = null;
        
        if (auth()->check() && auth()->user()->participante) {
            $equipo = auth()->user()->participante->equipo;
        }

        return view('event', compact('eventos', 'equipo'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create events')) {
            abort(403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'categoria' => 'nullable|string|max:255',
        ]);

        Evento::create($request->all());

        return redirect()->route('event')->with('success', 'Evento creado correctamente.');
    }

    public function update(Request $request, $id)
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

        return redirect()->route('event')->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('delete events')) {
            abort(403);
        }

        $evento = Evento::findOrFail($id);
        $evento->delete();

        return redirect()->route('event')->with('success', 'Evento eliminado correctamente.');
    }
}
