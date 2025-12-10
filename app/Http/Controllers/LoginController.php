<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            \App\Services\AuditLogger::log('login', \App\Models\User::class, Auth::id(), 'Inicio de sesión exitoso');

            return redirect()->route('index');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        Auth::logout();

        if ($userId) {
            \App\Services\AuditLogger::log('logout', \App\Models\User::class, $userId, 'Cierre de sesión');
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
