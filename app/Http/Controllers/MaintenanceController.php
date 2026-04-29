<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class MaintenanceController extends Controller
{
    public function toggle()
    {
        $active = Cache::get('maintenance_mode', false);

        if ($active) {
            Cache::forget('maintenance_mode');
            $msg = 'Modo mantenimiento desactivado. El sitio ya es accesible para todos.';
        } else {
            Cache::put('maintenance_mode', true);
            $msg = 'Modo mantenimiento activado. Solo los administradores pueden acceder al sitio.';
        }

        return back()->with('success', $msg);
    }
}
