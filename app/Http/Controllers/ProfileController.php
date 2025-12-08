<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        $user = auth()->user();
        return view('perfil', compact('user'));
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'institucion' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->update(['profile_photo_path' => $path]);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($user->participant) {
            $user->participant->update([
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

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado correctamente.');
    }
}
