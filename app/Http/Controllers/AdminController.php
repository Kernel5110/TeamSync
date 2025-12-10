<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Institucion;
use App\Models\Carrera;

use App\Services\AuditLogger;

class AdminController extends Controller
{
    // ... existing methods ...

    public function settings()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $instituciones = Institucion::all();
        $carreras = Carrera::all();

        return view('admin.settings', compact('instituciones', 'carreras'));
    }

    public function storeInstitucion(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate(['nombre' => 'required|string|unique:instituciones,nombre']);

        $institucion = Institucion::create(['nombre' => $request->nombre]);

        AuditLogger::log('create', Institucion::class, $institucion->id, "Institución creada: {$institucion->nombre}");

        return back()->with('success', 'Institución agregada correctamente.');
    }

    public function destroyInstitucion($id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $institucion = Institucion::findOrFail($id);
        $institucion->delete();

        AuditLogger::log('delete', Institucion::class, $id, "Institución eliminada: {$institucion->nombre}");

        return back()->with('success', 'Institución eliminada correctamente.');
    }

    public function storeCarrera(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate(['nombre' => 'required|string|unique:carreras,nombre']);

        $carrera = Carrera::create(['nombre' => $request->nombre]);

        AuditLogger::log('create', Carrera::class, $carrera->id, "Carrera creada: {$carrera->nombre}");

        return back()->with('success', 'Carrera agregada correctamente.');
    }

    public function destroyCarrera($id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $carrera = Carrera::findOrFail($id);
        $carrera->delete();

        AuditLogger::log('delete', Carrera::class, $id, "Carrera eliminada: {$carrera->nombre}");

        return back()->with('success', 'Carrera eliminada correctamente.');
    }
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

        return redirect()->route('profile.show')->with('success', 'Juez creado correctamente.');
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

        // 🚨 CAMBIO CLAVE: Se eliminó la validación que requería el rol 'juez'.
        // Ahora, cualquier usuario (participante) puede ser asignado como juez al evento.

        // Check if already assigned
        // Asume que Evento::jueces() es la relación Many-to-Many
        if (!$evento->jueces()->where('user_id', $user->id)->exists()) {
            $evento->jueces()->attach($user->id);
            return back()->with('success', 'Juez asignado correctamente.');
        }

        return back()->with('info', 'El juez ya está asignado a este evento.');
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
            'name' => 'required|string|max:255',
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,juez,participante', // Added participante as it might be useful
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
