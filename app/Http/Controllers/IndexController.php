<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * Mostrar la página de inicio (index)
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        
        
        return view('index');
    }
}
