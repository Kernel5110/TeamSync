<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $logs = \App\Models\AuditLog::with('user')->latest()->paginate(20);

        return view('admin.logs', compact('logs'));
    }
}
