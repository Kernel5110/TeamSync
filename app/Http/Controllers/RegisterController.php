<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\Institution;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $carreras = Career::all();
        $instituciones = Institution::all();
        return view('registrar', compact('carreras', 'instituciones'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'institucion' => 'required|string|max:255',
            'carrera' => 'required|exists:careers,id',
            'correo' => 'required|string|email|max:255|unique:users,email',
            'contraseña' => 'required|string|min:8',
            'control_number' => 'nullable|string|regex:/^[0-9]{7,12}$/',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'institucion.required' => 'La institución es obligatoria.',
            'carrera.required' => 'Selecciona una carrera válida.',
            'carrera.exists' => 'La carrera seleccionada no es válida.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
            'correo.unique' => 'Este correo ya está registrado.',
            'contraseña.required' => 'La contraseña es obligatoria.',
            'contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'control_number.regex' => 'El número de control debe contener entre 7 y 12 dígitos numéricos.',
        ]);

        $user = User::create([
            'name' => $request->nombre . ' ' . $request->apellido,
            'email' => $request->correo,
            'password' => Hash::make($request->contraseña),
        ]);

        Participant::create([
            'user_id' => $user->id,
            'career_id' => $request->carrera,
            'institution' => $request->institucion,
            'control_number' => $request->control_number ?? 'N/A',
        ]);

        $user->assignRole('competidor'); // Should rely on Spatie Permission

        Auth::login($user);

        return redirect()->route('index'); // Assuming 'home' route exists, or '/'
    }
}
