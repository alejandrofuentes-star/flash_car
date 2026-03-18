<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class VehicleController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $vehicles = Vehicle::with('category')->orderBy('name', 'asc')->get();
        return view('vehiculos.index', compact('categories', 'vehicles'));
    }

    public function catalogo()
    {
        $vehicles = Vehicle::with('category')
                           ->where('active', 1)
                           ->orderBy('available', 'desc')
                           ->orderBy('name', 'asc')
                           ->get();
        return view('catalogo.index', compact('vehicles'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name', 'asc')->get();
        return view('vehiculos.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'image'        => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'passengers'   => 'required|integer|min:1|max:50',
            'fuel_capacity'=> 'required|numeric|min:0',
            'brand'        => 'nullable|string|max:100',
            'model'        => 'nullable|string|max:100',
            'year'         => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'plate_number' => 'nullable|string|max:20|unique:vehicles',
            'transmission' => 'required|in:manual,automatic',
            'available'    => 'boolean',
            'active'       => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.webp';
            $imagePath = 'vehicles/' . $imageName;

            $manager = new ImageManager(new Driver());
            $img     = $manager->read($image->getRealPath());
            $img->cover(600, 280);

            Storage::disk('public')->put($imagePath, $img->toWebp(80)->toString());
            $validated['image_path'] = $imagePath;
        }

        $baseSlug = Str::slug($validated['name']);
        $slug     = $baseSlug;
        $counter  = 1;
        while (Vehicle::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $validated['slug']      = $slug;
        $validated['available'] = $request->has('available');
        $validated['active']    = $request->has('active');

        $vehicle = Vehicle::create($validated);

        return redirect()->route('vehiculos.index')
            ->with('success', "Vehículo '{$vehicle->name}' creado exitosamente");
    }

    public function show($id)
    {
        $vehicle = Vehicle::with('category')->findOrFail($id);
        return view('vehiculos.detalles', compact('vehicle'));
    }

    public function detalle($id)
    {
        $vehicle = Vehicle::with('category')->where('active', 1)->findOrFail($id);
        return view('catalogo.detalle', compact('vehicle'));
    }

    public function edit($id)
    {
        $vehicle    = Vehicle::findOrFail($id);
        $categories = Category::active()->orderBy('name', 'asc')->get();
        return view('vehiculos.editar_vehiculo', compact('vehicle', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'image'        => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'passengers'   => 'required|integer|min:1|max:50',
            'fuel_capacity'=> 'required|numeric|min:0',
            'brand'        => 'nullable|string|max:100',
            'model'        => 'nullable|string|max:100',
            'year'         => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'plate_number' => ['nullable', 'string', 'max:20', Rule::unique('vehicles')->ignore($vehicle->id)],
            'transmission' => 'required|in:manual,automatic',
            'available'    => 'boolean',
            'active'       => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($vehicle->image_path && Storage::disk('public')->exists($vehicle->image_path)) {
                Storage::disk('public')->delete($vehicle->image_path);
            }

            $image     = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.webp';
            $imagePath = 'vehicles/' . $imageName;

            $manager = new ImageManager(new Driver());
            $img     = $manager->read($image->getRealPath());
            $img->cover(600, 280);

            Storage::disk('public')->put($imagePath, $img->toWebp(80)->toString());
            $validated['image_path'] = $imagePath;
        }

        if ($vehicle->name !== $validated['name']) {
            $baseSlug = Str::slug($validated['name']);
            $slug     = $baseSlug;
            $counter  = 1;
            while (Vehicle::where('slug', $slug)->where('id', '!=', $vehicle->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        $validated['available'] = $request->has('available');
        $validated['active']    = $request->has('active');

        $vehicle->update($validated);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo actualizado exitosamente');
    }

    public function destroy($id)
    {
        $vehicle     = Vehicle::findOrFail($id);
        $vehicleName = $vehicle->name;
        $vehicle->delete();

        return redirect()->route('vehiculos.index')
            ->with('success', "Vehículo '{$vehicleName}' eliminado exitosamente");
    }
}