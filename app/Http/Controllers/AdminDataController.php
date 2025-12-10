<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\Carrera;
use Illuminate\Http\Request;

class AdminDataController extends Controller
{
    public function index()
    {
        $instituciones = Institucion::all();
        $carreras = Carrera::all();
        return view('admin.data', compact('instituciones', 'carreras'));
    }

    // Instituciones
    public function storeInstitucion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:instituciones,nombre',
        ]);

        $institucion = Institucion::create($request->all());

        \App\Services\AuditLogger::log('create', Institucion::class, $institucion->id, "Institución creada: {$institucion->nombre}");

        return back()->with('success', 'Institución creada correctamente.');
    }

    public function updateInstitucion(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:instituciones,nombre,' . $id,
        ]);

        $institucion = Institucion::findOrFail($id);
        $institucion->update($request->all());

        \App\Services\AuditLogger::log('update', Institucion::class, $institucion->id, "Institución actualizada: {$institucion->nombre}");

        return back()->with('success', 'Institución actualizada correctamente.');
    }

    public function destroyInstitucion($id)
    {
        $institucion = Institucion::findOrFail($id);
        $institucion->delete();

        \App\Services\AuditLogger::log('delete', Institucion::class, $id, "Institución eliminada: {$institucion->nombre}");

        return back()->with('success', 'Institución eliminada correctamente.');
    }

    // Carreras
    public function storeCarrera(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:carreras,nombre',
        ]);

        $carrera = Carrera::create($request->all());

        \App\Services\AuditLogger::log('create', Carrera::class, $carrera->id, "Carrera creada: {$carrera->nombre}");

        return back()->with('success', 'Carrera creada correctamente.');
    }

    public function updateCarrera(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:carreras,nombre,' . $id,
        ]);

        $carrera = Carrera::findOrFail($id);
        $carrera->update($request->all());

        \App\Services\AuditLogger::log('update', Carrera::class, $carrera->id, "Carrera actualizada: {$carrera->nombre}");

        return back()->with('success', 'Carrera actualizada correctamente.');
    }

    public function destroyCarrera($id)
    {
        $carrera = Carrera::findOrFail($id);
        $carrera->delete();

        \App\Services\AuditLogger::log('delete', Carrera::class, $id, "Carrera eliminada: {$carrera->nombre}");

        return back()->with('success', 'Carrera eliminada correctamente.');
    }
}
