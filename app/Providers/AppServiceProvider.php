<?php

namespace App\Providers;

use App\Models\NotificacionLeida;
use App\Models\Renta;
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

                // Rentas con la devolución vencida que siguen reservadas / por entregar / abiertas:
                // el estado no se actualizó y los reportes no las cuentan bien hasta que se cierre.
                // La clave incluye el estado: si cambia a otro estado sin cerrar, vuelve a avisar.
                $etiquetasEstado = [
                    'reserva_confirmada' => 'Reserva confirmada',
                    'proxima_entrega'    => 'Próxima entrega',
                    'contrato_abierto'   => 'Contrato abierto',
                ];
                $vencidas = Renta::devolucionVencida()
                    ->with('vehicle:id,name')
                    ->orderBy('fecha_devolucion')
                    ->get(['id', 'vehicle_id', 'nombre_completo', 'fecha_devolucion', 'estado'])
                    ->map(function ($renta) use ($hoy, $etiquetasEstado) {
                        $fecha = Carbon::parse($renta->fecha_devolucion->toDateString(), Vehicle::TZ_OPERACION)->startOfDay();
                        $dias  = (int) $fecha->diffInDays($hoy);

                        return [
                            'clave'   => "renta_vencida:{$renta->id}:{$fecha->toDateString()}:{$renta->estado}",
                            'icono'   => 'bi-exclamation-triangle-fill',
                            'titulo'  => 'Devolución vencida sin cerrar (' . ($etiquetasEstado[$renta->estado] ?? $renta->estado) . ')',
                            'detalle' => "#{$renta->id} {$renta->nombre_completo}"
                                . ($renta->vehicle ? " · {$renta->vehicle->name}" : '')
                                . ' · devolvía ' . $fecha->format('d/m/Y')
                                . ($dias > 0 ? " (hace {$dias} " . ($dias === 1 ? 'día' : 'días') . ')' : ' (hoy)'),
                            'urgente' => $dias >= 1,
                            'url'     => route('rentas.show', $renta->id),
                        ];
                    });
                $notificaciones = $notificaciones->concat($vencidas);

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
