<?php

namespace App\Models;

use App\Services\RentaPricingCalculator;
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
        'desglose_original' => 'array',
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

        // Foto fija del desglose al generar la renta (historial); nunca se vuelve a modificar
        static::creating(function ($renta) {
            $renta->desglose_original ??= $renta->armarDesgloseOriginal();
        });
    }

    // Fechas, días, precios y desglose de la renta tal como están ahora (null si el auto no tiene categoría)
    public function armarDesgloseOriginal(): ?array
    {
        $category = $this->vehicle?->category;
        if (!$category) {
            return null;
        }

        return [
            'fecha_entrega'    => $this->fecha_entrega->format('Y-m-d'),
            'hora_entrega'     => substr((string) $this->hora_entrega, 0, 5),
            'fecha_devolucion' => $this->fecha_devolucion->format('Y-m-d'),
            'hora_devolucion'  => substr((string) $this->hora_devolucion, 0, 5),
            'total_dias'       => (int) $this->total_dias,
            'costo_total'      => (float) $this->costo_total,
            'vehiculo'         => $this->vehicle->name,
            'categoria'        => $category->name,
            'generado_at'      => now(Vehicle::TZ_OPERACION)->toDateTimeString(),
        ] + RentaPricingCalculator::desgloseDeRenta($this, $category);
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
