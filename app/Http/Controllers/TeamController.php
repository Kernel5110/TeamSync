<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $participante = $user->participante;
        $equipo = $participante ? $participante->equipo : null;
        $eventos = Evento::all(); // For creating a team
        
        $allTeams = null;
        if ($user->hasRole('admin')) {
            $allTeams = Equipo::with(['participantes.user', 'evento'])->get();
        }

        return view('team', compact('equipo', 'eventos', 'allTeams'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('edit teams')) {
            abort(403);
        }

        $equipo = Equipo::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'evento_id' => 'required|exists:eventos,id',
        ]);

        $equipo->update([
            'nombre' => $request->nombre,
            'evento_id' => $request->evento_id,
        ]);

        return redirect()->route('team')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('delete teams')) {
            abort(403);
        }

        $equipo = Equipo::findOrFail($id);
        
        // Optional: Handle participants before deleting (e.g., set team_id to null)
        foreach ($equipo->participantes as $participante) {
            $participante->update(['equipo_id' => null, 'rol' => null]);
        }

        $equipo->delete();

        return redirect()->route('team')->with('success', 'Equipo eliminado correctamente.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'evento_id' => 'required|exists:eventos,id',
        ]);

        $user = Auth::user();
        $participante = $user->participante;

        if (!$participante) {
            return back()->with('error', 'Debes completar tu registro de participante primero.');
        }

        if ($participante->equipo_id) {
            return back()->with('error', 'Ya perteneces a un equipo.');
        }

        $equipo = Equipo::create([
            'nombre' => $request->nombre,
            'evento_id' => $request->evento_id,
        ]);

        // Assign creator to team with a default role
        $participante->update([
            'equipo_id' => $equipo->id,
            'rol' => 'Líder', // Default role
        ]);

        return redirect()->route('team')->with('success', 'Equipo creado exitosamente.');
    }

    public function addMember(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = Auth::user();
        $participante = $user->participante;
        $equipo = $participante->equipo;

        if (!$equipo) {
            return back()->with('error', 'No tienes un equipo.');
        }

        $newMemberUser = User::where('email', $request->email)->first();
        $newMemberParticipante = $newMemberUser->participante;

        if (!$newMemberParticipante) {
            return back()->with('error', 'El usuario no ha completado su registro como participante.');
        }

        if ($newMemberParticipante->equipo_id) {
            return back()->with('error', 'El usuario ya pertenece a un equipo.');
        }

        $newMemberParticipante->update([
            'equipo_id' => $equipo->id,
            'rol' => 'Miembro', // Default role for new members
        ]);

        return back()->with('success', 'Miembro agregado exitosamente.');
    }
}
