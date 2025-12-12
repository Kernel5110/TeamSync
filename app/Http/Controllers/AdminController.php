<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Institution;
use App\Models\Career;

use App\Services\AuditLogger;

class AdminController extends Controller
{

    public function settings()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $institutions = Institution::all();
        $careers = Career::all();

        return view('admin.settings', ['instituciones' => $institutions, 'carreras' => $careers]);
    }

    public function teams(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $query = $request->input('query');
        $teams = \App\Models\Team::with(['event', 'participants.user']);

        if ($query) {
            $teams->where('name', 'LIKE', "%{$query}%") 
                  ->orWhereHas('event', function($q) use ($query) {
                      $q->where('name', 'LIKE', "%{$query}%"); 
                  });
        }

        $teams = $teams->paginate(10);

        return view('admin.teams', compact('teams', 'query'));
    }

    public function createJudge(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('juez');

        return redirect()->route('profile.show')->with('success', 'Juez creado correctamente.');
    }

    public function assignJudge(Request $request, $eventId)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $event = Event::findOrFail($eventId);
        $user = User::findOrFail($request->user_id);

        if (!$user->hasRole('juez') && !$user->hasRole('admin')) {
             return back()->with('error', 'El usuario no tiene el rol de juez.');
        }

        if ($event->status_manual === 'Finalizado' || $event->status === 'Finalizado') {
             return back()->with('error', 'No se puede asignar jueces a un evento finalizado.');
        }

        if (!$event->judges()->where('user_id', $user->id)->exists()) {
            $event->judges()->attach($user->id);
            return back()->with('success', 'Juez asignado correctamente.');
        }

        return back()->with('info', 'El juez ya está asignado a este evento.');
    }

    public function removeJudge(Request $request, $eventId, $userId)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $event = Event::findOrFail($eventId);
        $user = User::findOrFail($userId);

        if (!$event->judges()->where('user_id', $user->id)->exists()) {
             return back()->with('error', 'El juez no está asignado a este evento.');
        }


        $event->judges()->detach($user->id);
        
        AuditLogger::log('remove_judge', Event::class, $event->id, "Juez removido: {$user->email} del evento {$event->name}");

        return back()->with('success', 'Juez removido del evento correctamente.');
    }

    public function users(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $query = $request->input('query');
        $users = User::with('roles');

        if ($query) {
            $users->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            });
        }

        $users = $users->paginate(15);

        return view('admin.users', compact('users', 'query'));
    }

    public function storeUser(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,juez',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        AuditLogger::log('create', User::class, $user->id, "Usuario creado: {$user->email} con rol {$request->role}");

        return redirect()->route('admin.users')->with('success', 'Usuario creado correctamente.');
    }

    public function destroyUser($id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        if (auth()->id() == $id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        AuditLogger::log('delete', User::class, $user->id, "Usuario eliminado: {$user->email}");

        return redirect()->route('admin.users')->with('success', 'Usuario eliminado correctamente.');
    }

    public function updateUser(Request $request, $id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,juez,participante,competidor',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8',
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->syncRoles([$request->role]);

        AuditLogger::log('update', User::class, $user->id, "Usuario actualizado: {$user->email}, nuevo rol {$request->role}");

        return redirect()->route('admin.users')->with('success', 'Usuario actualizado correctamente.');
    }
}
