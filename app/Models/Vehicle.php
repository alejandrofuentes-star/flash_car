<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Vehicle extends Model
{
    // Estados de Renta que mantienen ocupado el vehículo hasta su fecha/hora de devolución.
    // Cancelada, finalizada, devolución exitosa, etc. liberan el auto.
    public const ESTADOS_OCUPAN = ['reserva_confirmada', 'proxima_entrega', 'pendiente_pago', 'contrato_abierto'];

    // Las horas de las rentas se capturan en hora local; la app corre en UTC.
    public const TZ_OPERACION = 'America/Mexico_City';

    // Horas que el auto sigue bloqueado después de la devolución (revisión, limpieza, etc.)
    public const HORAS_PREPARACION = 8;

    // Días de anticipación para avisar (campana del header) del pago al proveedor
    public const DIAS_AVISO_PAGO_PROVEEDOR = 3;

    protected $fillable = [
        'name', 'category_id', 'slug', 'image_path', 'passengers',
        'fuel_capacity', 'brand', 'model', 'year', 'plate_number', 'proveedor', 'fecha_pago_proveedor', 'costo_por_dia',
        'transmission', 'available', 'mostrar_en_catalogo', 'active', 'motivo_inactivo', 'city', 'mileage', 'next_verification',
    ];

    protected $casts = [
        'passengers'    => 'integer',
        'year'          => 'integer',
        'fuel_capacity' => 'decimal:2',
        'costo_por_dia' => 'decimal:2',
        'available'     => 'boolean',
        'mostrar_en_catalogo' => 'boolean',
        'active'        => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vehicle) {
            if (empty($vehicle->slug)) {
                $baseSlug = Str::slug($vehicle->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $vehicle->slug = $slug;
            }
        });

        static::updating(function ($vehicle) {
            if ($vehicle->isDirty('name') && !$vehicle->isDirty('slug')) {
                $baseSlug = Str::slug($vehicle->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->where('id', '!=', $vehicle->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $vehicle->slug = $slug;
            }
        });

        // Las rentas sin costo_por_dia propio usan el del vehículo: se recalcula su total_general
        static::updated(function ($vehicle) {
            if ($vehicle->wasChanged('costo_por_dia')) {
                $vehicle->rentas()->whereNull('costo_por_dia')->get()->each(function ($renta) use ($vehicle) {
                    $renta->setRelation('vehicle', $vehicle);
                    Renta::whereKey($renta->id)->update(['total_general' => $renta->calcularTotalGeneral()]);
                });
            }
        });

        static::deleting(function ($vehicle) {
            if ($vehicle->image_path && Storage::disk('public')->exists($vehicle->image_path)) {
                Storage::disk('public')->delete($vehicle->image_path);
            }
        });
    }

    public function category() { return $this->belongsTo(Category::class); }
    public function scopeActive($query) { return $query->where('active', true); }
    public function scopeAvailable($query) { return $query->where('available', true); }

    // Autos cuyo pago al proveedor cae entre hoy y los próximos DIAS_AVISO_PAGO_PROVEEDOR días (hora local)
    public function scopePagoProveedorProximo($query)
    {
        $hoy = now(self::TZ_OPERACION)->startOfDay();

        return $query->whereNotNull('fecha_pago_proveedor')
            ->whereBetween('fecha_pago_proveedor', [
                $hoy->toDateString(),
                $hoy->copy()->addDays(self::DIAS_AVISO_PAGO_PROVEEDOR)->toDateString(),
            ]);
    }
    public function isAvailable() { return $this->available === true; }
    public function isActive() { return $this->active === true; }

    // Rentas en curso en este momento: ya pasó la entrega y aún no termina la devolución
    // más las horas de preparación
    public function rentasEnCurso()
    {
        $ahora = now(self::TZ_OPERACION)->format('Y-m-d H:i:s');

        return $this->hasMany(Renta::class)
            ->whereIn('estado', self::ESTADOS_OCUPAN)
            ->whereRaw('TIMESTAMP(fecha_entrega, hora_entrega) <= ?', [$ahora])
            ->whereRaw('TIMESTAMP(fecha_devolucion, hora_devolucion) + INTERVAL ' . self::HORAS_PREPARACION . ' HOUR > ?', [$ahora]);
    }

    // Activo, marcado como disponible y sin renta en curso en este momento
    public function scopeDisponibleAhora($query)
    {
        return $query->where('active', true)
            ->where('available', true)
            ->whereDoesntHave('rentasEnCurso');
    }

    // Activo, marcado como disponible y sin rentas que se crucen con el rango pedido,
    // contando las horas de preparación después de cada devolución (la existente y la pedida).
    // $entrega / $devolucion: 'Y-m-d H:i' en hora local. $exceptoRentaId: para no chocar
    // con la misma renta al editarla.
    public function scopeLibreEntre($query, $entrega, $devolucion, $exceptoRentaId = null)
    {
        $devolucionMasPreparacion = \Carbon\Carbon::parse($devolucion)->addHours(self::HORAS_PREPARACION)->format('Y-m-d H:i');

        return $query->where('active', true)
            ->where('available', true)
            ->whereDoesntHave('rentas', function ($r) use ($entrega, $devolucionMasPreparacion, $exceptoRentaId) {
                $r->whereIn('estado', self::ESTADOS_OCUPAN)
                  ->whereRaw('TIMESTAMP(fecha_entrega, hora_entrega) < ?', [$devolucionMasPreparacion])
                  ->whereRaw('TIMESTAMP(fecha_devolucion, hora_devolucion) + INTERVAL ' . self::HORAS_PREPARACION . ' HOUR > ?', [$entrega])
                  ->when($exceptoRentaId, fn($q) => $q->where('id', '!=', $exceptoRentaId));
            });
    }

    public function estaLibreEntre($entrega, $devolucion, $exceptoRentaId = null)
    {
        return static::whereKey($this->id)->libreEntre($entrega, $devolucion, $exceptoRentaId)->exists();
    }

    // Fecha/hora (local) en que el auto vuelve a estar disponible: devolución de la renta
    // en curso + horas de preparación; null si no está rentado ahora
    public function getOcupadoHastaAttribute()
    {
        $renta = $this->rentasEnCurso
            ->sortByDesc(fn($r) => $r->fecha_devolucion->format('Y-m-d') . ' ' . $r->hora_devolucion)
            ->first();

        return $renta
            ? \Carbon\Carbon::parse($renta->fecha_devolucion->format('Y-m-d') . ' ' . $renta->hora_devolucion, self::TZ_OPERACION)
                ->addHours(self::HORAS_PREPARACION)
            : null;
    }

    public function getDisponibleAhoraAttribute()
    {
        return $this->active && $this->available && $this->ocupado_hasta === null;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) return Storage::url($this->image_path);
        return null;
    }

    public function hasImage()
    {
        return $this->image_path && Storage::disk('public')->exists($this->image_path);
    }

    public function getFullNameAttribute()
    {
        return $this->brand && $this->model && $this->year
            ? "{$this->brand} {$this->model} {$this->year}"
            : $this->name;
    }

    public function getFormattedFuelCapacityAttribute()
    {
        return number_format($this->fuel_capacity, 1) . ' L';
    }

    public function states()
    {
        return $this->belongsToMany(State::class);
    }

    public function rentas()
    {
        return $this->hasMany(Renta::class);
    }

    public function images()
    {
        return $this->hasMany(VehicleImage::class)->orderBy('orden');
    }
}