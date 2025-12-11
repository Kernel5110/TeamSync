<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Career;
use Illuminate\Http\Request;
use App\Services\AuditLogger;

class AdminDataController extends Controller
{
    public function storeInstitution(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate(['name' => 'required|string|unique:institutions,name']);

        $institution = Institution::create(['name' => $request->name]);

        AuditLogger::log('create', Institution::class, $institution->id, "Institución creada: {$institution->name}");

        return back()->with('success', 'Institución agregada correctamente.');
    }

    public function updateInstitution(Request $request, $id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $institution = Institution::findOrFail($id);
        
        $request->validate(['name' => 'required|string|unique:institutions,name,' . $id]);

        $institution->update(['name' => $request->name]);

        AuditLogger::log('update', Institution::class, $institution->id, "Institución actualizada: {$institution->name}");

        return back()->with('success', 'Institución actualizada correctamente.');
    }

    public function destroyInstitution($id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $institution = Institution::findOrFail($id);
        $institution->delete();

        AuditLogger::log('delete', Institution::class, $id, "Institución eliminada: {$institution->name}");

        return back()->with('success', 'Institución eliminada correctamente.');
    }

    public function storeCareer(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate(['name' => 'required|string|unique:careers,name']);

        $career = Career::create(['name' => $request->name]);

        AuditLogger::log('create', Career::class, $career->id, "Carrera creada: {$career->name}");

        return back()->with('success', 'Carrera agregada correctamente.');
    }

    public function updateCareer(Request $request, $id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $career = Career::findOrFail($id);

        $request->validate(['name' => 'required|string|unique:careers,name,' . $id]);

        $career->update(['name' => $request->name]);

        AuditLogger::log('update', Career::class, $career->id, "Carrera actualizada: {$career->name}");

        return back()->with('success', 'Carrera actualizada correctamente.');
    }

    public function destroyCareer($id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $career = Career::findOrFail($id);
        $career->delete();

        AuditLogger::log('delete', Career::class, $id, "Carrera eliminada: {$career->name}");

        return back()->with('success', 'Carrera eliminada correctamente.');
    }
}
