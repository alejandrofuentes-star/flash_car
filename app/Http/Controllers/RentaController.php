<?php

namespace App\Http\Controllers;

use App\Mail\NuevaRentaAdmin;
use App\Mail\RentaSolicitada;
use App\Models\Renta;
use App\Models\SiteSetting;
use App\Models\SiteStat;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RentaController extends Controller
{
    public function create($vehicle_id)
    {
        $vehicle = Vehicle::with('category')->findOrFail($vehicle_id);
        
        $states = \App\Models\State::with('deliveryPoints')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $categories = \App\Models\Category::where('active', 1)->orderBy('name')->get();

        return view('catalogo.create_renta', compact('vehicle', 'states', 'categories'));
    }

    public function store(Request $request)
    {
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
            'costo_total'      => 'required|numeric|min:0',
        ]);

        $renta = Renta::create($validated);
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

    public function index()
    {
        $rentas = Renta::with('vehicle')->orderBy('created_at', 'desc')->paginate(15);
        return view('rentas.index', compact('rentas'));
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
            'estado' => 'required|in:pendiente,confirmada,cancelada,completada'
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
            'estado'           => 'required|in:pendiente,confirmada,cancelada,completada',
        ]);

        $renta->update($validated);

        return redirect()->route('rentas.show', $renta->id)
            ->with('success', 'Renta actualizada correctamente.');
    }
}
