<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class RentaController extends Controller
{
    public function create($vehicleId)
    {
        $vehicle = Vehicle::with('category')->where('active', 1)->findOrFail($vehicleId);
        return view('catalogo.create_renta', compact('vehicle'));
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

        Renta::create($validated);

        return redirect()->route('inicio')
            ->with('success', '¡Tu solicitud de renta fue enviada correctamente! Pronto nos pondremos en contacto contigo.');
    }

    public function index()
    {
        $rentas = Renta::with('vehicle')->orderBy('created_at', 'desc')->get();
        return view('rentas.index', compact('rentas'));
    }

    public function show($id)
    {
        $renta = Renta::with('vehicle')->findOrFail($id);
        return view('rentas.show', compact('renta'));
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
}
