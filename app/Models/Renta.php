<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renta extends Model
{
    protected $fillable = [
        'vehicle_id', 'nombre_completo', 'telefono', 'correo',
        'ciudad', 'fecha_entrega', 'hora_entrega', 'lugar_entrega',
        'fecha_devolucion', 'hora_devolucion', 'lugar_devolucion',
        'num_pasajeros', 'total_dias', 'costo_total', 'estado',
    ];

    protected $casts = [
        'fecha_entrega'    => 'date',
        'fecha_devolucion' => 'date',
        'costo_total'      => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
