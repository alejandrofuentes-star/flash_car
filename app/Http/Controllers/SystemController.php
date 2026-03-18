<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemController extends Controller
{
    public function index()
    {
        return view('super-admin.cache');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        return back()->with('success', 'Caché de aplicación limpiada correctamente.');
    }

    public function clearConfig()
    {
        Artisan::call('config:clear');
        return back()->with('success', 'Caché de configuración limpiada correctamente.');
    }

    public function clearRoutes()
    {
        Artisan::call('route:clear');
        return back()->with('success', 'Caché de rutas limpiada correctamente.');
    }

    public function clearViews()
    {
        Artisan::call('view:clear');
        return back()->with('success', 'Caché de vistas limpiada correctamente.');
    }

    public function clearAll()
    {
        Artisan::call('optimize:clear');
        return back()->with('success', 'Todo el caché fue limpiado correctamente.');
    }
}