<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Solicitud de renta rechazada por falta de auto libre en las fechas pedidas (demanda no atendida).
// Se registra en RentaController::registrarNoAtendida(); los reintentos del mismo cliente para la
// misma categoría y fechas en el mismo día suman `intentos` en lugar de crear otro registro.
class SolicitudNoAtendida extends Model
{
    protected $table = 'solicitudes_no_atendidas';

    protected $fillable = [
        'category_id', 'vehicle_id',
        'fecha_entrega', 'hora_entrega', 'fecha_devolucion', 'hora_devolucion',
        'total_dias', 'nombre_completo', 'correo', 'origen', 'intentos',
    ];

    protected $casts = [
        'fecha_entrega'    => 'date',
        'fecha_devolucion' => 'date',
        'total_dias'       => 'integer',
        'intentos'         => 'integer',
    ];

    public const ORIGENES = [
        'pago_categoria' => 'Pago con tarjeta (sin autos libres en la categoría)',
        'pago_vehiculo'  => 'Pago con tarjeta (auto elegido ocupado)',
        'formulario'     => 'Formulario sin pago (sin autos libres)',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
}
