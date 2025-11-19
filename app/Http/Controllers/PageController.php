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
        return view('event');
    }

    /**
     * Mostrar la página de equipo
     */
    public function team()
    {
        return view('team');
    }
}
