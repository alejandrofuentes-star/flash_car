<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Historial de disponibilidad de la flota (alta, cambios de active/available y baja de cada vehículo).
// Lo llenan los eventos de Vehicle; el estado de un auto en una fecha es su último registro con
// desde <= esa fecha. Lo usa ReporteController::calcularFlotaPorDia() para no reconstruir meses
// pasados con la flota actual.
class VehiculoDisponibilidad extends Model
{
    protected $table = 'vehiculo_disponibilidad';

    public const UPDATED_AT = null;

    protected $fillable = ['vehicle_id', 'active', 'available', 'desde', 'reconstruido'];

    protected $casts = [
        'active'       => 'boolean',
        'available'    => 'boolean',
        'reconstruido' => 'boolean',
        'desde'        => 'datetime',
    ];

    public static function registrar(Vehicle $vehicle, ?bool $active = null, ?bool $available = null): void
    {
        static::create([
            'vehicle_id' => $vehicle->id,
            'active'     => $active ?? (bool) $vehicle->active,
            'available'  => $available ?? (bool) $vehicle->available,
            'desde'      => now(),
        ]);
    }
}
