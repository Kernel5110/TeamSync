<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for login/admin routes to avoid locking out admins
        if ($request->is('login') || $request->is('admin/*') || $request->is('logout')) {
            return $next($request);
        }

        $maintenance = Setting::where('key', 'maintenance_mode')->value('value');

        if ($maintenance == '1' && !Auth::check()) {
             return response()->view('errors.maintenance', [], 503);
        }
        
        if ($maintenance == '1' && Auth::check() && !Auth::user()->hasRole('admin')) {
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
