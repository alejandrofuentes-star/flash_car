<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renta extends Model
{
    protected $fillable = [
        'id_flash_car', 'vehicle_id', 'nombre_completo', 'telefono', 'correo',
        'ciudad', 'fecha_entrega', 'hora_entrega', 'lugar_entrega',
        'fecha_devolucion', 'hora_devolucion', 'lugar_devolucion',
        'num_pasajeros', 'total_dias', 'costo_total', 'precio_dia', 'precio_semana', 'precio_mes', 'costo_por_dia', 'descuento', 'metodo_pago', 'payment_intent_id', 'monto_anticipo', 'estado',
        'mail_enviado', 'mail_enviado_at',
    ];

    protected $casts = [
        'fecha_entrega'    => 'date',
        'fecha_devolucion' => 'date',
        'costo_total'      => 'decimal:2',
        'monto_anticipo'   => 'decimal:2',
        'precio_dia'       => 'decimal:2',
        'precio_semana'    => 'decimal:2',
        'precio_mes'       => 'decimal:2',
        'costo_por_dia'    => 'decimal:2',
        'descuento'        => 'decimal:2',
        'total_general'    => 'decimal:2',
        'mail_enviado'     => 'boolean',
        'mail_enviado_at'  => 'datetime',
    ];

    protected static function booted()
    {
        // total_general se guarda siempre recalculado (store, update, webhook, edición rápida)
        static::saving(function ($renta) {
            if ($renta->isDirty('vehicle_id')) {
                $renta->unsetRelation('vehicle');
            }
            $renta->total_general = $renta->calcularTotalGeneral();
        });
    }

    // Costo por día de la renta o, si no tiene, el del vehículo (null si ninguno lo tiene)
    public function costoPorDiaAplicado()
    {
        return $this->costo_por_dia ?? $this->vehicle?->costo_por_dia;
    }

    // costo_total − (total_dias × costo por día del auto) − descuento
    public function calcularTotalGeneral(): float
    {
        $costoDia  = $this->costoPorDiaAplicado();
        $costoAuto = $costoDia !== null ? round($this->total_dias * $costoDia, 2) : 0;

        return round((float) $this->costo_total - $costoAuto - (float) ($this->descuento ?? 0), 2);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
