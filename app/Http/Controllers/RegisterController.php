<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Participante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $carreras = Carrera::all();
        return view('registrar', compact('carreras'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'institucion' => 'required|string|max:255',
            'carrera' => 'required|exists:carreras,id',
            'correo' => 'required|string|email|max:255|unique:users,email',
            'contraseña' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->nombre . ' ' . $request->apellido,
            'email' => $request->correo,
            'password' => Hash::make($request->contraseña),
        ]);

        Participante::create([
            'usuario_id' => $user->id,
            'carrera_id' => $request->carrera,
            'institucion' => $request->institucion,
        ]);

        Auth::login($user);

        return redirect()->route('index'); // Assuming 'home' route exists, or '/'
    }
}
