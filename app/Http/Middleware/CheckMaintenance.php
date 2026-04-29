<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        if (!Cache::get('maintenance_mode', false)) {
            return $next($request);
        }

        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return $next($request);
        }

        if ($request->routeIs('login') || $request->is('login') || $request->routeIs('logout')) {
            return $next($request);
        }

        return response()->view('maintenance', [], 503);
    }
}
