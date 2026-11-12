<?php

namespace App\Models;

use App\Services\RentaPricingCalculator;
use Illuminate\Database\Eloquent\Model;

class Renta extends Model
{
    // Motivos de cancelación. 'falta_disponibilidad' cuenta como demanda no atendida en reportes.
    public const MOTIVOS_CANCELACION = [
        'falta_disponibilidad' => 'Falta de disponibilidad (no había auto)',
        'cliente_desistio'     => 'El cliente desistió',
        'pago_no_completado'   => 'Pago o garantía no completados',
        'otro'                 => 'Otro motivo',
    ];

    protected $fillable = [
        'id_flash_car', 'vehicle_id', 'nombre_completo', 'telefono', 'correo',
        'ciudad', 'fecha_entrega', 'hora_entrega', 'lugar_entrega',
        'fecha_devolucion', 'hora_devolucion', 'lugar_devolucion',
        'num_pasajeros', 'total_dias', 'costo_total', 'precio_dia', 'precio_semana', 'precio_mes', 'costo_por_dia', 'descuento', 'metodo_pago', 'payment_intent_id', 'monto_anticipo', 'estado', 'motivo_cancelacion',
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

            // El motivo solo aplica a rentas canceladas
            if ($renta->estado !== 'cancelada') {
                $renta->motivo_cancelacion = null;
            }
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

    // Estados de una renta que todavía no se entrega o sigue abierta. Si la devolución ya pasó y la
    // renta sigue en uno de estos, nadie actualizó el estado (alerta en la campana del header).
    public const ESTADOS_SIN_CERRAR = ['reserva_confirmada', 'proxima_entrega', 'contrato_abierto'];

    // Rentas sin cerrar cuya fecha + hora de devolución (hora de operación) ya pasó
    public function scopeDevolucionVencida($query)
    {
        $ahora = now(Vehicle::TZ_OPERACION);

        return $query->whereIn('estado', self::ESTADOS_SIN_CERRAR)
            ->where(fn ($q) => $q->whereDate('fecha_devolucion', '<', $ahora->toDateString())
                ->orWhere(fn ($q) => $q->whereDate('fecha_devolucion', $ahora->toDateString())
                    ->where('hora_devolucion', '<', $ahora->format('H:i:s'))));
    }
}
