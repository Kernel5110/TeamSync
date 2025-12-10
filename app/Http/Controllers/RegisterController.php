<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Institucion;
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
        $instituciones = Institucion::all();
        return view('registrar', compact('carreras', 'instituciones'));
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
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'institucion.required' => 'La institución es obligatoria.',
            'carrera.required' => 'Selecciona una carrera válida.',
            'carrera.exists' => 'La carrera seleccionada no es válida.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
            'correo.unique' => 'Este correo ya está registrado.',
            'contraseña.required' => 'La contraseña es obligatoria.',
            'contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
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

        $user->assignRole('competidor');

        Auth::login($user);

        return redirect()->route('index'); // Assuming 'home' route exists, or '/'
    }
}
