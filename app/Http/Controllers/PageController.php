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
     * Mostrar la página de start
     */
    public function start()
    {
        if (auth()->user()->hasRole('admin')) {
            $stats = [
                'eventos' => \App\Models\Evento::count(),
                'usuarios' => \App\Models\User::count(),
                'equipos' => \App\Models\Equipo::count(),
                'jueces' => \App\Models\User::role('juez')->count(),
            ];
            return view('start', compact('stats'));
        }
        
        if (auth()->user()->hasRole('juez')) {
            $judgeEvents = auth()->user()->judgeEvents()->with(['equipos' => function($q) {
                $q->withCount(['evaluations' => function($query) {
                    $query->where('user_id', auth()->id());
                }]);
            }])->get();
            
            return view('start', compact('judgeEvents'));
        }
        
        // Participant Logic
        $user = auth()->user();
        $participante = $user->participant;
        $equipo = $participante ? $participante->equipo : null;
        $evento = $equipo ? $equipo->evento : null;
        
        return view('start', compact('equipo', 'evento'));
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

        $teams = $teams->paginate(10);

        return view('admin.teams', compact('teams', 'query'));
    }
}
