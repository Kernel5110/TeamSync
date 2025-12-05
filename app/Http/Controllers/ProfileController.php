<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('perfil', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'institucion' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($user->participante) {
            $user->participante->update([
                'institucion' => $request->institucion,
            ]);
        } else {
            // Create participant record if it doesn't exist
            Participante::create([
                'usuario_id' => $user->id,
                'institucion' => $request->institucion ?? 'No especificada',
                'carrera_id' => 1, // Default or handle appropriately
            ]);
        }

        return redirect()->route('perfil')->with('success', 'Perfil actualizado correctamente.');
    }
}
