<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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

        $evento = Evento::findOrFail($evento_id);
        $user = User::findOrFail($request->user_id);

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
