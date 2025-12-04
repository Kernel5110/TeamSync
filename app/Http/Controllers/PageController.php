<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Mostrar la página de inicio
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Mostrar la página de login
     */
    public function login()
    {
        return view('login');
    }

    /**
     * Mostrar la página de start
     */
    public function start()
    {
        return view('start');
    }

    /**
     * Mostrar la página de eventos
     */
    public function event()
    {
        $eventos = \App\Models\Evento::all();
        $equipo = null;
        
        if (auth()->check() && auth()->user()->participante) {
            $equipo = auth()->user()->participante->equipo;
        }

        return view('event', compact('eventos', 'equipo'));
    }

    /**
     * Mostrar la página de equipo
     */
    public function team()
    {
        return view('team');
    }
    /**
     * Mostrar la página de registrarse
     */
    public function registrar(){
        return view('registrar');
    }
    public function perfil()
    {
        $user = auth()->user();
        return view('perfil', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'institucion' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($user->participante) {
            $user->participante->update([
                'institucion' => $request->institucion,
            ]);
        } else {
            // Create participant record if it doesn't exist (though it should for competitors)
            \App\Models\Participante::create([
                'usuario_id' => $user->id,
                'institucion' => $request->institucion ?? 'No especificada',
                'carrera_id' => 1, // Default or handle appropriately
            ]);
        }

        return redirect()->route('perfil')->with('success', 'Perfil actualizado correctamente.');
    }

    public function storeEvent(Request $request)
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

        \App\Models\Evento::create($request->all());

        return redirect()->route('event')->with('success', 'Evento creado correctamente.');
    }

    public function updateEvent(Request $request, $id)
    {
        if (!auth()->user()->can('edit events')) {
            abort(403);
        }

        $evento = \App\Models\Evento::findOrFail($id);

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

    public function deleteEvent($id)
    {
        if (!auth()->user()->can('delete events')) {
            abort(403);
        }

        $evento = \App\Models\Evento::findOrFail($id);
        $evento->delete();

        return redirect()->route('event')->with('success', 'Evento eliminado correctamente.');
    }

    public function createJudge(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        $user->assignRole('juez');

        return redirect()->route('perfil')->with('success', 'Juez creado correctamente.');
    }

    public function assignJudge(Request $request, $evento_id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $evento = \App\Models\Evento::findOrFail($evento_id);
        $user = \App\Models\User::findOrFail($request->user_id);

        if (!$user->hasRole('juez')) {
             return back()->with('error', 'El usuario seleccionado no es un juez.');
        }

        // Check if already assigned
        if (!$evento->jueces()->where('user_id', $user->id)->exists()) {
            $evento->jueces()->attach($user->id);
            return back()->with('success', 'Juez asignado correctamente.');
        }

        return back()->with('info', 'El juez ya está asignado a este evento.');
    }
}
