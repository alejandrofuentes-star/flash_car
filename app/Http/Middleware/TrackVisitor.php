<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Solo rastrear carga de páginas completas (no AJAX ni rutas internas)
        if (!$request->ajax() && !$request->is('visitantes', 'visitantes/leave', 'heartbeat') && $request->session()->isStarted()) {
            $visitors = Cache::get('active_visitors', []);
            $now      = now()->timestamp;
            $visitors[$request->session()->getId()] = $now;
            $visitors = array_filter($visitors, fn($t) => ($now - $t) <= 10);
            Cache::put('active_visitors', $visitors, 60);
        }

        return $response;
    }
}
