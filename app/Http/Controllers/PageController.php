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
        return back()->with('success', 'Juez asignado correctamente.');
    }

    public function adminTeams(Request $request)
    {
        $query = $request->input('query');
        $teams = \App\Models\Equipo::with(['evento', 'participantes.user']);

        if ($query) {
            $teams->where('nombre', 'LIKE', "%{$query}%")
                  ->orWhereHas('evento', function($q) use ($query) {
                      $q->where('nombre', 'LIKE', "%{$query}%");
                  });
        }

        $teams = $teams->get();

        return view('admin.teams', compact('teams', 'query'));
    }
}
