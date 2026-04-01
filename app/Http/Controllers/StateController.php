<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\DeliveryPoint;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index()
    {
        $states = State::with('deliveryPoints')->orderBy('name')->get();
        return view('states.index', compact('states'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:states']);
        State::create(['name' => $request->name, 'active' => true]);
        return back()->with('success', 'Estado agregado correctamente.');
    }

    public function update(Request $request, State $state)
    {
        $request->validate(['name' => 'required|string|max:100|unique:states,name,' . $state->id]);
        $state->update(['name' => $request->name]);
        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(State $state)
    {
        $state->delete();
        return back()->with('success', 'Estado eliminado correctamente.');
    }

    public function toggleActive(State $state)
    {
        $state->update(['active' => !$state->active]);
        return back()->with('success', 'Estado actualizado.');
    }

    // Puntos de entrega
    public function storePoint(Request $request, State $state)
    {
        $request->validate([
            'name'    => 'required|string|max:150',
        ]);
        $state->deliveryPoints()->create([
            'name'    => $request->name,
            'active'  => true,
        ]);
        return back()->with('success', 'Punto de entrega agregado.');
    }

    public function destroyPoint(DeliveryPoint $point)
    {
        $point->delete();
        return back()->with('success', 'Punto eliminado correctamente.');
    }

    public function togglePoint(DeliveryPoint $point)
    {
        $point->update(['active' => !$point->active]);
        return back()->with('success', 'Punto actualizado.');
    }
}