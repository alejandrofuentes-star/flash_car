<?php

namespace App\Http\Controllers;

use App\Mail\NuevaRentaAdmin;
use App\Mail\RentaSolicitada;
use App\Models\Category;
use App\Models\Renta;
use App\Models\SiteSetting;
use App\Models\SiteStat;
use App\Models\Vehicle;
use App\Services\RentaPricingCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class RentaController extends Controller
{
    // Campos de la renta editables desde el detalle (rentas/show → tarjetas con lápiz): columna => etiqueta
    public const CAMPOS_RAPIDOS = [
        'costo_por_dia' => 'Costo por día',
        'descuento'     => 'Descuentos',
    ];

    public function createGeneral()
    {
        $categories = \App\Models\Category::where('active', 1)->orderBy('name')->get();

        // Primera imagen disponible por categoría (solo para mostrar en sidebar)
        $firstVehicleImages = Vehicle::where('active', 1)
            ->where('available', 1)
            ->whereNotNull('image_path')
            ->whereIn('category_id', $categories->pluck('id'))
            ->orderBy('id')
            ->get(['id', 'category_id', 'image_path'])
            ->groupBy('category_id')
            ->map(fn($vs) => $vs->first()->image_path);

        $states = \App\Models\State::with('deliveryPoints')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $pagoTarjetaActivo = SiteSetting::get('pago_tarjeta', '1') === '1';
        $anticipoTipo      = SiteSetting::get('anticipo_tipo', 'fijo');
        $anticipoMonto     = (float) SiteSetting::get('anticipo_monto', '0');

        return view('catalogo.create_renta_general', compact('categories', 'firstVehicleImages', 'states', 'pagoTarjetaActivo', 'anticipoTipo', 'anticipoMonto'));
    }

    public function create($vehicle_id)
    {
        $vehicle = Vehicle::with('category')->findOrFail($vehicle_id);
        
        $states = \App\Models\State::with('deliveryPoints')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $categories = \App\Models\Category::where('active', 1)->orderBy('name')->get();
        $pagoTarjetaActivo = SiteSetting::get('pago_tarjeta', '1') === '1';
        $anticipoTipo      = SiteSetting::get('anticipo_tipo', 'fijo');
        $anticipoMonto     = (float) SiteSetting::get('anticipo_monto', '0');

        return view('catalogo.create_renta', compact('vehicle', 'states', 'categories', 'pagoTarjetaActivo', 'anticipoTipo', 'anticipoMonto'));
    }

    // Rango 'Y-m-d H:i' (hora local) de la renta pedida, para revisar cruces con otras rentas
    private function rangoRenta($datos): array
    {
        // data_get funciona igual con arrays del request y con el modelo Renta
        $fecha = fn($f) => $f instanceof \DateTimeInterface ? $f->format('Y-m-d') : (string) $f;
        $hora  = fn($h) => substr((string) $h, 0, 5);

        return [
            $fecha(data_get($datos, 'fecha_entrega')) . ' ' . $hora(data_get($datos, 'hora_entrega')),
            $fecha(data_get($datos, 'fecha_devolucion')) . ' ' . $hora(data_get($datos, 'hora_devolucion')),
        ];
    }

    public function createPaymentIntent(Request $request)
    {
        // Formulario general: resolver vehículo a partir de categoría (misma lógica que store())
        if (!$request->filled('vehicle_id') && $request->filled('category_id')) {
            $vehicleResuelto = Vehicle::where('category_id', $request->category_id)
                ->when(
                    $request->filled(['fecha_entrega', 'hora_entrega', 'fecha_devolucion', 'hora_devolucion']),
                    fn($q) => $q->libreEntre(...$this->rangoRenta($request->all())),
                    fn($q) => $q->where('active', 1)->where('available', 1)
                )
                ->first();
            if ($vehicleResuelto) {
                $request->merge(['vehicle_id' => $vehicleResuelto->id]);
            }
        }

        $validated = $request->validate([
            'vehicle_id'       => 'required|integer|exists:vehicles,id',
            'category_id'      => 'nullable|integer|exists:categories,id',
            'nombre_completo'  => 'required|string|max:150',
            'telefono'         => 'nullable|string|max:20',
            'correo'           => 'required|email|max:100',
            'ciudad'           => 'nullable|string|max:100',
            'fecha_entrega'    => 'required|date',
            'hora_entrega'     => 'required|string',
            'lugar_entrega'    => 'nullable|string|max:255',
            'fecha_devolucion' => 'required|date|after:fecha_entrega',
            'hora_devolucion'  => 'required|string',
            'lugar_devolucion' => 'nullable|string|max:255',
            'num_pasajeros'    => 'nullable|integer|min:1',
        ]);

        // El monto a cobrar nunca se confía del navegador: se recalcula en servidor a
        // partir de fechas/horas y el precio real de la categoría (incluye tolerancia de 2h).
        $vehicle = Vehicle::with('category')->find($validated['vehicle_id']);
        if (!$vehicle || !$vehicle->category) {
            return response()->json(['error' => 'No se encontró un vehículo disponible para calcular el costo.'], 422);
        }

        // Se valida antes de cobrar: el auto no puede tener otra renta que se cruce con esas fechas
        if (!$vehicle->estaLibreEntre(...$this->rangoRenta($validated))) {
            return response()->json(['error' => __('form.vehicle_not_available')], 422);
        }

        try {
            $calculo = RentaPricingCalculator::calcular(
                $validated['fecha_entrega'], $validated['hora_entrega'],
                $validated['fecha_devolucion'], $validated['hora_devolucion'],
                $vehicle->category
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $montoAnticipo = $this->calcularMontoAnticipo($calculo['costo_total']);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Guardamos todos los datos del formulario en el PaymentIntent: si el navegador
            // del cliente nunca llega a hacer el submit final, el webhook puede recuperar
            // la renta a partir de esta metadata en lugar de perder el registro por completo.
            // total_dias/costo_total van con el valor recalculado en servidor, no el del navegador.
            $metadata = collect($validated)
                ->filter(fn ($v) => $v !== null && $v !== '')
                ->map(fn ($v) => (string) $v)
                ->all();
            $metadata['total_dias']  = (string) $calculo['total_dias'];
            $metadata['costo_total'] = (string) $calculo['costo_total'];
            foreach ($calculo['precios'] as $campo => $precio) {
                $metadata[$campo] = (string) $precio;
            }

            $intent = PaymentIntent::create([
                'amount'   => (int) round($montoAnticipo * 100), // centavos
                'currency' => 'mxn',
                'description' => 'Renta Flash Car - ' . $validated['nombre_completo'],
                'receipt_email' => $validated['correo'],
                'metadata'  => $metadata,
            ]);

            return response()->json([
                'client_secret'    => $intent->client_secret,
                'payment_intent_id' => $intent->id,
                'monto_anticipo'    => $montoAnticipo,
                'costo_total'       => $calculo['costo_total'],
                'total_dias'        => $calculo['total_dias'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Monto a cobrar ahora mismo por Stripe (anticipo total o parcial), a partir del
     * costo total ya recalculado en servidor. Replica la regla de public/js del formulario
     * (window.flashCarAnticipo): sin anticipo configurado, se cobra el costo total completo.
     */
    private function calcularMontoAnticipo(float $costoTotal): float
    {
        $tipo  = SiteSetting::get('anticipo_tipo', 'fijo');
        $monto = (float) SiteSetting::get('anticipo_monto', '0');

        if ($monto <= 0) {
            return $costoTotal;
        }

        return $tipo === 'porcentaje'
            ? round($costoTotal * $monto) / 100
            : min($monto, $costoTotal);
    }

    public function store(Request $request)
    {
        // Formulario general: resolver vehículo a partir de categoría
        if (!$request->filled('vehicle_id') && $request->filled('category_id')) {
            // Preferir un auto libre en esas fechas; si el pago ya se cobró (tarjeta) y en el inter se
            // ocupó, se asigna cualquier auto activo de la categoría para no perder la renta pagada.
            $vehicle = $request->filled(['fecha_entrega', 'hora_entrega', 'fecha_devolucion', 'hora_devolucion'])
                ? Vehicle::where('category_id', $request->category_id)->libreEntre(...$this->rangoRenta($request->all()))->first()
                : null;
            if (!$vehicle && SiteSetting::get('pago_tarjeta', '1') === '1') {
                $vehicle = Vehicle::where('category_id', $request->category_id)
                    ->where('available', 1)
                    ->where('active', 1)
                    ->first();
            }
            if (!$vehicle) {
                return back()
                    ->withErrors(['category_id' => __('form.no_vehicles_category')])
                    ->withInput();
            }
            $request->merge(['vehicle_id' => $vehicle->id]);
        }

        $validated = $request->validate([
            'vehicle_id'       => 'required|exists:vehicles,id',
            'nombre_completo'  => 'required|string|max:150',
            'telefono'         => 'required|string|max:20',
            'correo'           => 'required|email|max:100',
            'ciudad'           => 'required|string|max:100',
            'fecha_entrega'    => 'required|date|after_or_equal:today',
            'hora_entrega'     => 'required',
            'lugar_entrega'    => 'required|string|max:255',
            'fecha_devolucion' => 'required|date|after:fecha_entrega',
            'hora_devolucion'  => 'required',
            'lugar_devolucion' => 'required|string|max:255',
            'num_pasajeros'    => 'required|integer|min:1',
            'total_dias'       => 'required|integer|min:1',
            'costo_total'        => 'required|numeric|min:0',
            'metodo_pago'        => SiteSetting::get('pago_tarjeta', '1') === '1' ? 'required|in:tarjeta' : 'nullable',
            'payment_intent_id'  => SiteSetting::get('pago_tarjeta', '1') === '1' ? 'required|string' : 'nullable',
        ]);

        // total_dias/costo_total nunca se confían del navegador: se recalculan en servidor
        // a partir de fechas/horas y el precio real de la categoría (incluye la tolerancia de 2h).
        $vehicle = Vehicle::with('category')->find($validated['vehicle_id']);
        if (!$vehicle || !$vehicle->category) {
            return back()
                ->withErrors(['vehicle_id' => 'El vehículo no tiene una categoría con precios configurados.'])
                ->withInput();
        }

        // Sin pago con tarjeta no hay cobro previo que proteger: se rechaza si el auto ya está rentado en esas fechas
        if (SiteSetting::get('pago_tarjeta', '1') !== '1' && !$vehicle->estaLibreEntre(...$this->rangoRenta($validated))) {
            return back()
                ->withErrors(['vehicle_id' => __('form.vehicle_not_available')])
                ->withInput();
        }

        try {
            $calculo = RentaPricingCalculator::calcular(
                $validated['fecha_entrega'], $validated['hora_entrega'],
                $validated['fecha_devolucion'], $validated['hora_devolucion'],
                $vehicle->category
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['fecha_devolucion' => $e->getMessage()])->withInput();
        }

        $validated['total_dias']  = $calculo['total_dias'];
        $validated['costo_total'] = $calculo['costo_total'];
        $validated += $calculo['precios'];

        if (SiteSetting::get('pago_tarjeta', '1') === '1') {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $intent = PaymentIntent::retrieve($validated['payment_intent_id']);
                if ($intent->status !== 'succeeded') {
                    return back()->withErrors(['pago' => 'El pago no fue completado. Intenta de nuevo.']);
                }
                $validated['monto_anticipo'] = $intent->amount / 100;
            } catch (\Exception $e) {
                return back()->withErrors(['pago' => 'No se pudo verificar el pago: ' . $e->getMessage()]);
            }
        }

        $renta = DB::transaction(function () use ($validated) {
            $validated['id_flash_car'] = (Renta::lockForUpdate()->max('id_flash_car') ?? 66624) + 1;
            return Renta::create($validated);
        });
        $renta->load('vehicle');

        SiteStat::addOne('total_reservations');

        try {
            Mail::to($renta->correo)->send(new RentaSolicitada($renta));
            $renta->update(['mail_enviado' => true, 'mail_enviado_at' => now()]);
        } catch (\Throwable $e) {
            \Log::error('Error enviando correo de renta: ' . $e->getMessage());
        }

        try {
            Mail::to(SiteSetting::get('admin_notification_email', 'flashcarental@gmail.com'))->send(new NuevaRentaAdmin($renta));
        } catch (\Throwable $e) {
            \Log::error('Error enviando correo admin de renta: ' . $e->getMessage());
        }

        return redirect()->route('inicio')
            ->with('success', '¡Tu solicitud de renta fue enviada correctamente! Pronto nos pondremos en contacto contigo.')
            ->with('reserva_ok', true);
    }

    public function index(Request $request)
    {
        $buscar = $request->query('buscar');
        $registro = $request->query('registro');
        $correo = $request->query('correo');
        $fechaDevolucion = $request->query('fecha_devolucion');

        $rentas = Renta::with('vehicle.category')
            ->when($buscar, fn($query) => $query->where('nombre_completo', 'like', '%' . $buscar . '%'))
            ->when($registro, fn($query) => $query->where('id_flash_car', $registro))
            ->when($correo, fn($query) => $query->where('correo', 'like', '%' . $correo . '%'))
            ->when($fechaDevolucion, fn($query) => $query->whereDate('fecha_devolucion', $fechaDevolucion))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('rentas.index', compact('rentas', 'buscar', 'registro', 'correo', 'fechaDevolucion'));
    }

    public function show($id)
    {
        $renta = Renta::with('vehicle.category')->findOrFail($id);

        // Desglose con los precios guardados en la renta; las rentas anteriores a 2026-09-29 no los tienen
        // y se usan los precios actuales de la categoría
        $desglose = null;
        if ($category = $renta->vehicle?->category) {
            $preciosGuardados = $renta->precio_dia !== null;
            $precios = $preciosGuardados
                ? (new Category)->forceFill([
                    'price_per_day'   => $renta->precio_dia,
                    'price_per_week'  => $renta->precio_semana,
                    'price_per_month' => $renta->precio_mes,
                ])
                : $category;

            try {
                $cargoExtra = RentaPricingCalculator::calcular(
                    $renta->fecha_entrega->format('Y-m-d'), (string) $renta->hora_entrega,
                    $renta->fecha_devolucion->format('Y-m-d'), (string) $renta->hora_devolucion,
                    $category
                )['cargo_extra'];
            } catch (\InvalidArgumentException $e) {
                $cargoExtra = false;
            }

            // Solo se cobra el día extra si la renta abarca más de un día de calendario
            $totalDias  = (int) $renta->total_dias;
            $cargoExtra = $cargoExtra && $totalDias > 1
                && $renta->fecha_entrega->diffInDays($renta->fecha_devolucion) > 0;

            // Con día extra: tarifa de los días base + 1 día a precio diario. Como el total se calcula
            // sobre todos los días juntos, si al sumar el extra se completa una semana/mes la diferencia
            // aparece como ajuste por tarifa (así la suma siempre da el total).
            $diasBase = $cargoExtra ? $totalDias - 1 : $totalDias;
            $partidas = RentaPricingCalculator::desglose($diasBase, $precios);
            $subtotal = round(array_sum(array_column($partidas, 'subtotal')), 2);
            $calculado = round(array_sum(array_column(RentaPricingCalculator::desglose($totalDias, $precios), 'subtotal')), 2);
            $diaExtra = $cargoExtra ? (float) $precios->price_per_day : 0.0;

            $desglose  = [
                'partidas'    => $partidas,
                'dias_base'   => $diasBase,
                'subtotal'    => $subtotal,
                'cargo_extra' => $cargoExtra,
                'dia_extra'   => $diaExtra,
                'ajuste'      => round($calculado - $subtotal - $diaExtra, 2),
                'calculado'   => $calculado,
                'coincide'    => abs($calculado - (float) $renta->costo_total) < 0.01,
                'guardados'   => $preciosGuardados,
            ];
        }

        return view('rentas.show', compact('renta', 'desglose'));
    }

    public function reenviarCorreo($id)
    {
        $renta = Renta::with('vehicle')->findOrFail($id);

        try {
            Mail::to($renta->correo)->send(new RentaSolicitada($renta));
            $renta->update(['mail_enviado' => true, 'mail_enviado_at' => now()]);
        } catch (\Throwable $e) {
            \Log::error('Error reenviando correo renta #' . $id . ': ' . $e->getMessage());
            return back()->with('error', 'No se pudo enviar el correo: ' . $e->getMessage());
        }

        try {
            Mail::to(SiteSetting::get('admin_notification_email', 'flashcarental@gmail.com'))->send(new NuevaRentaAdmin($renta));
        } catch (\Throwable $e) {
            \Log::error('Error reenviando correo admin renta #' . $id . ': ' . $e->getMessage());
        }

        $adminEmail = SiteSetting::get('admin_notification_email', 'flashcarental@gmail.com');
        return back()->with('success', 'Correo reenviado a ' . $renta->correo . ' y notificación al administrador (' . $adminEmail . ').');
    }

    public function updateEstado(Request $request, $id)
    {
        $renta = Renta::findOrFail($id);
        $request->validate([
            'estado' => 'required|in:reserva_confirmada,proxima_entrega,pendiente_pago,contrato_abierto,contrato_finalizado,devolucion_exitosa,dano_faltante,garantia_pendiente,cancelada',
        ]);
        $renta->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado actualizado correctamente');
    }

    // Edición rápida desde el detalle (Resumen de Costo); vacío = null
    // (en costo_por_dia, null = se muestra el costo del vehículo)
    public function updateCampoRapido(Request $request, $id, string $campo)
    {
        $renta = Renta::findOrFail($id);

        $reglas = ['nullable', 'numeric', 'min:0', 'max:99999999.99'];
        if ($campo === 'descuento') {
            $reglas[] = 'lte:' . (float) $renta->costo_total;
        }
        $validated = $request->validate(
            [$campo => $reglas],
            [$campo . '.lte' => 'El descuento no puede ser mayor al costo total de la renta.'],
            [$campo => mb_strtolower(self::CAMPOS_RAPIDOS[$campo])]
        );

        $renta->update([$campo => $request->filled($campo) ? $validated[$campo] : null]);

        return back()->with('success', 'Se actualizó ' . mb_strtolower(self::CAMPOS_RAPIDOS[$campo]) . ' correctamente');
    }

    public function edit($id)
    {
        $renta = Renta::with(['vehicle.category'])->findOrFail($id);
        
        $states = \App\Models\State::with('deliveryPoints')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        // Solo vehículos libres en las fechas de esta renta (sin contar la propia renta);
        // el vehículo actual de la renta siempre se incluye para no perderlo del select.
        [$entrega, $devolucion] = $this->rangoRenta($renta);
        $vehicles = Vehicle::where(fn($q) => $q->libreEntre($entrega, $devolucion, $renta->id))
            ->orWhere('id', $renta->vehicle_id)
            ->orderBy('name')
            ->get(['id', 'name', 'plate_number', 'category_id']);

        $categories = Category::where('active', 1)
            ->orWhere('id', $renta->vehicle?->category_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('rentas.edit', compact('renta', 'states', 'vehicles', 'categories'));
    }

    public function destroy($id)
    {
        $renta = Renta::findOrFail($id);
        $renta->delete();

        SiteStat::where('key', 'total_reservations')->decrement('value');

        return redirect()->route('rentas.index')->with('success', 'Renta eliminada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $renta = Renta::findOrFail($id);

        $validated = $request->validate([
            'vehicle_id'       => 'required|exists:vehicles,id',
            'nombre_completo'  => 'required|string|max:255',
            'telefono'         => 'required|string|max:20',
            'correo'           => 'required|email|max:100',
            'ciudad'           => 'required|string|max:100',
            'fecha_entrega'    => 'required|date',
            'hora_entrega'     => 'required',
            'lugar_entrega'    => 'required|string|max:255',
            'fecha_devolucion' => 'required|date|after:fecha_entrega',
            'hora_devolucion'  => 'required',
            'lugar_devolucion' => 'required|string|max:255',
            'num_pasajeros'    => 'required|integer|min:1',
            'estado'           => 'required|in:reserva_confirmada,proxima_entrega,pendiente_pago,contrato_abierto,contrato_finalizado,devolucion_exitosa,dano_faltante,garantia_pendiente,cancelada',
        ]);

        // Aquí solo se edita la información general: total_dias, costo_total, precios, costo por día
        // y descuentos se quedan como se registraron (no se recalculan aunque cambien fechas o vehículo)
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        // Si cambia el vehículo o las fechas (y la renta sigue ocupando el auto), el vehículo no puede
        // tener otra renta que se cruce con el nuevo rango
        $rangoNuevo  = $this->rangoRenta($validated);
        $cambioAlgo  = (int) $vehicle->id !== (int) $renta->vehicle_id || $rangoNuevo !== $this->rangoRenta($renta);
        if ($cambioAlgo && in_array($validated['estado'], Vehicle::ESTADOS_OCUPAN)
            && !$vehicle->estaLibreEntre($rangoNuevo[0], $rangoNuevo[1], $renta->id)) {
            return back()
                ->withErrors(['vehicle_id' => "El vehículo '{$vehicle->name}' ya tiene otra renta en esas fechas."])
                ->withInput();
        }

        $renta->update($validated);

        return redirect()->route('rentas.show', $renta->id)
            ->with('success', 'Renta actualizada correctamente.');
    }
}
