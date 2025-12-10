<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        $user = auth()->user();
        $users = null;
        $instituciones = null;
        $carreras = null;
        
        if ($user->hasRole('admin')) {
            $users = \App\Models\User::with('roles')->get();
            $instituciones = \App\Models\Institution::all();
            $carreras = \App\Models\Career::all();
        }
        return view('perfil', compact('user', 'users', 'instituciones', 'carreras'));
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'institution' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', // 2MB Max
            'expertise' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->update(['profile_photo_path' => $path]);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'expertise' => $request->expertise,
        ]);

        if ($user->participant) {
            $user->participant->update([
                'institution' => $request->institucion,
            ]);
        } else {
            // Create participant record if it doesn't exist (only if not admin/judge purely)
             if (!$user->hasRole('admin') && !$user->hasRole('juez')) {
                Participant::create([
                    'user_id' => $user->id,
                    'institution' => $request->institucion ?? 'No especificada',
                    'carrera_id' => 1, // Default or handle appropriately
                ]);
            }
        }

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado correctamente.');
    }
}
