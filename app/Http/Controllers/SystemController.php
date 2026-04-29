<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    public function migrations()
    {
        $ran = DB::table('migrations')->pluck('migration')->toArray();

        $files = collect(glob(database_path('migrations/*.php')))
            ->map(fn($f) => pathinfo($f, PATHINFO_FILENAME))
            ->sortDesc()
            ->values();

        $migrations = $files->map(fn($name) => [
            'name'   => $name,
            'status' => in_array($name, $ran) ? 'ran' : 'pending',
        ]);

        $pendingCount = $migrations->where('status', 'pending')->count();

        return view('super-admin.migrations', compact('migrations', 'pendingCount'));
    }

    public function runMigrations()
    {
        Artisan::call('migrate', ['--force' => true]);
        $output = trim(Artisan::output());

        if (empty($output)) {
            $output = 'No había migraciones pendientes.';
        }

        return back()->with('success', nl2br(e($output)));
    }

    public function uploadMigration(Request $request)
    {
        $request->validate([
            'migration_file' => 'required|file|max:512',
        ]);

        $file      = $request->file('migration_file');
        $extension = $file->getClientOriginalExtension();
        $filename  = $file->getClientOriginalName();

        if ($extension !== 'php') {
            return back()->with('error', 'Solo se permiten archivos .php');
        }

        if (!preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_\w+\.php$/', $filename)) {
            return back()->with('error', 'Nombre inválido. Formato esperado: YYYY_MM_DD_HHMMSS_nombre.php');
        }

        if (file_exists(database_path('migrations/' . $filename))) {
            return back()->with('error', "Ya existe una migración con el nombre «{$filename}».");
        }

        $file->move(database_path('migrations'), $filename);

        return back()->with('success', "Migración «{$filename}» subida correctamente. Ya puedes ejecutar las pendientes.");
    }
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