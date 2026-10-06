<?php

namespace App\Providers;

use App\Models\NotificacionLeida;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Alertas de la campana del header admin
        View::composer('layout.header', function ($view) {
            $notificaciones = collect();

            if (Auth::check() && Auth::user()->hasAdminAccess()) {
                $hoy = now(Vehicle::TZ_OPERACION)->startOfDay();

                $notificaciones = Vehicle::pagoProveedorProximo()
                    ->orderBy('fecha_pago_proveedor')
                    ->get(['id', 'name', 'plate_number', 'proveedor', 'fecha_pago_proveedor', 'costo_por_dia'])
                    ->map(function ($vehicle) use ($hoy) {
                        $fecha = Carbon::parse($vehicle->fecha_pago_proveedor, Vehicle::TZ_OPERACION)->startOfDay();
                        $dias  = (int) $hoy->diffInDays($fecha);

                        return [
                            'clave'   => "pago_proveedor:{$vehicle->id}:{$fecha->toDateString()}",
                            'icono'   => 'bi-cash-coin',
                            'titulo'  => 'Pago a proveedor ' . match ($dias) {
                                0       => 'hoy',
                                1       => 'mañana',
                                default => "en {$dias} días",
                            },
                            'detalle' => $vehicle->name
                                . ($vehicle->plate_number ? " ({$vehicle->plate_number})" : '')
                                . ($vehicle->proveedor ? " · {$vehicle->proveedor}" : '')
                                . ' · ' . $fecha->format('d/m/Y'),
                            'urgente' => $dias <= 1,
                            'url'     => route('vehicles.show', $vehicle->id),
                        ];
                    });

                $leidas = NotificacionLeida::where('user_id', Auth::id())
                    ->whereIn('clave', $notificaciones->pluck('clave'))
                    ->pluck('clave')
                    ->flip();

                // No leídas primero, conservando el orden por fecha
                $notificaciones = $notificaciones
                    ->map(fn ($n) => $n + ['leida' => $leidas->has($n['clave'])])
                    ->sortBy(fn ($n) => $n['leida'] ? 1 : 0)
                    ->values();
            }

            $view->with('notificaciones', $notificaciones);
        });
    }
}
