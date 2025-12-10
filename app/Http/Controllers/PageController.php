<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Team;

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
                'eventos' => Event::count(),
                'usuarios' => \App\Models\User::count(),
                'equipos' => Team::count(),
                'jueces' => \App\Models\User::role('juez')->count(),
            ];
            return view('start', compact('stats'));
        }
        
        if (auth()->user()->hasRole('juez')) {
            $judgeEvents = auth()->user()->judgeEvents()->with(['teams' => function($q) {
                $q->withCount(['evaluations' => function($query) {
                    $query->where('user_id', auth()->id());
                }]);
            }])->get();
            
            return view('start', compact('judgeEvents'));
        }
        
        // Participant Logic
        $user = auth()->user();
        $participante = $user->participant;
        $equipo = $participante ? $participante->team : null;
        $evento = $equipo ? $equipo->event : null;
        
        return view('start', compact('equipo', 'evento'));
    }

}
