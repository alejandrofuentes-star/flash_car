<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $pendientes         = Renta::where('estado', 'pendiente')->count();
        $confirmadas        = Renta::where('estado', 'confirmada')->count();
        $vehiculos_disponibles = Vehicle::where('available', true)->count();
        $vehiculos_total    = Vehicle::count();
        $ingresos_mes       = Renta::whereIn('estado', ['confirmada', 'completada'])
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->sum('costo_total');

        // Últimas 8 solicitudes
        $recientes = Renta::with('vehicle')
                        ->orderBy('created_at', 'desc')
                        ->limit(8)
                        ->get();

        // Entregas próximas (hoy + 3 días)
        $proximas = Renta::with('vehicle')
                        ->whereIn('estado', ['pendiente', 'confirmada'])
                        ->whereBetween('fecha_entrega', [Carbon::today(), Carbon::today()->addDays(3)])
                        ->orderBy('fecha_entrega')
                        ->orderBy('hora_entrega')
                        ->get();

        return view('dashboard', compact(
            'pendientes', 'confirmadas',
            'vehiculos_disponibles', 'vehiculos_total',
            'ingresos_mes', 'recientes', 'proximas'
        ));
    }
}
