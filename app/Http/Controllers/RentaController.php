<?php

namespace App\Http\Controllers;

use App\Mail\NuevaRentaAdmin;
use App\Mail\RentaSolicitada;
use App\Models\Renta;
use App\Models\SiteSetting;
use App\Models\SiteStat;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class RentaController extends Controller
{
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

    public function createPaymentIntent(Request $request)
    {
        $validated = $request->validate([
            'amount'           => 'required|numeric|min:1',
            'vehicle_id'       => 'nullable|integer|exists:vehicles,id',
            'category_id'      => 'nullable|integer|exists:categories,id',
            'nombre_completo'  => 'required|string|max:150',
            'telefono'         => 'nullable|string|max:20',
            'correo'           => 'required|email|max:100',
            'ciudad'           => 'nullable|string|max:100',
            'fecha_entrega'    => 'nullable|date',
            'hora_entrega'     => 'nullable|string',
            'lugar_entrega'    => 'nullable|string|max:255',
            'fecha_devolucion' => 'nullable|date',
            'hora_devolucion'  => 'nullable|string',
            'lugar_devolucion' => 'nullable|string|max:255',
            'num_pasajeros'    => 'nullable|integer|min:1',
            'total_dias'       => 'nullable|integer|min:1',
            'costo_total'      => 'nullable|numeric|min:0',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Guardamos todos los datos del formulario en el PaymentIntent: si el navegador
            // del cliente nunca llega a hacer el submit final, el webhook puede recuperar
            // la renta a partir de esta metadata en lugar de perder el registro por completo.
            $metadata = collect($validated)
                ->except(['amount'])
                ->filter(fn ($v) => $v !== null && $v !== '')
                ->map(fn ($v) => (string) $v)
                ->all();

            $intent = PaymentIntent::create([
                'amount'   => (int) round($validated['amount'] * 100), // centavos
                'currency' => 'mxn',
                'description' => 'Renta Flash Car - ' . $validated['nombre_completo'],
                'receipt_email' => $validated['correo'],
                'metadata'  => $metadata,
            ]);

            return response()->json([
                'client_secret'    => $intent->client_secret,
                'payment_intent_id' => $intent->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function store(Request $request)
    {
        // Formulario general: resolver vehículo a partir de categoría
        if (!$request->filled('vehicle_id') && $request->filled('category_id')) {
            $vehicle = Vehicle::where('category_id', $request->category_id)
                ->where('available', 1)
                ->where('active', 1)
                ->first();
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

        $rentas = Renta::with('vehicle')
            ->when($buscar, fn($query) => $query->where('nombre_completo', 'like', '%' . $buscar . '%'))
            ->when($registro, fn($query) => $query->where('id', $registro))
            ->when($correo, fn($query) => $query->where('correo', 'like', '%' . $correo . '%'))
            ->when($fechaDevolucion, fn($query) => $query->whereDate('fecha_devolucion', $fechaDevolucion))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('rentas.index', compact('rentas', 'buscar', 'registro', 'correo', 'fechaDevolucion'));
    }

    public function show($id)
    {
        $renta = Renta::with('vehicle')->findOrFail($id);
        return view('rentas.show', compact('renta'));
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

    public function edit($id)
    {
        $renta = Renta::with(['vehicle.category'])->findOrFail($id);
        
        $states = \App\Models\State::with('deliveryPoints')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $vehicles = Vehicle::where('active', 1)->orderBy('name')->get();

        return view('rentas.edit', compact('renta', 'states', 'vehicles'));
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
            'total_dias'       => 'required|integer|min:1',
            'costo_total'      => 'required|numeric|min:0',
            'estado'           => 'required|in:reserva_confirmada,proxima_entrega,pendiente_pago,contrato_abierto,contrato_finalizado,devolucion_exitosa,dano_faltante,garantia_pendiente,cancelada',
        ]);

        $renta->update($validated);

        return redirect()->route('rentas.show', $renta->id)
            ->with('success', 'Renta actualizada correctamente.');
    }
}
