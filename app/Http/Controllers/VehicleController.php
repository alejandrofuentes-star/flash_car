<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Category;
use App\Models\SliderImage;
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

        $cities = Vehicle::whereNotNull('city')
                            ->where('city', '!=', '')
                            ->where('active', 1)
                            ->distinct()
                            ->pluck('city')
                            ->sort()
                            ->values();
                            
        $categories = \App\Models\Category::where('active', 1)->orderBy('name')->get();
        $passengers = Vehicle::where('active', 1)->distinct()->orderBy('passengers')->pluck('passengers');

        $sliderDesktop = SliderImage::desktop()->active()->orderBy('orden')->get();
        $sliderMobile  = SliderImage::mobile()->active()->orderBy('orden')->get();

        return view('catalogo.index', compact('vehicles', 'cities', 'categories', 'passengers', 'sliderDesktop', 'sliderMobile'));
    }

    public function buscar(Request $request)
    {
        $city            = $request->city;
        $fecha_entrega   = $request->fecha_entrega;
        $fecha_devolucion = $request->fecha_devolucion;


        $vehicles = Vehicle::with('category')
            ->where('active', 1)
            ->where('available', 1)
            // Filtrar por ciudad si se seleccionó
            ->when($city, fn($q) => $q->where('city', $city))
            // Excluir autos que ya tienen renta en ese rango de fechas
            ->when($fecha_entrega && $fecha_devolucion, function($q) use ($fecha_entrega, $fecha_devolucion) {
                $q->whereDoesntHave('rentas', function($r) use ($fecha_entrega, $fecha_devolucion) {
                    $r->whereIn('estado', ['pendiente', 'confirmada'])
                    ->where('fecha_entrega', '<=', $fecha_devolucion)
                    ->where('fecha_devolucion', '>=', $fecha_entrega);
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        $cities = Vehicle::whereNotNull('city')
            ->where('city', '!=', '')
            ->where('active', 1)
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        $categories = \App\Models\Category::where('active', 1)->orderBy('name')->get();
        $passengers = Vehicle::where('active', 1)->distinct()->orderBy('passengers')->pluck('passengers');

        $sliderDesktop = SliderImage::desktop()->active()->orderBy('orden')->get();
        $sliderMobile  = SliderImage::mobile()->active()->orderBy('orden')->get();

        return view('catalogo.index', compact('vehicles', 'cities', 'city', 'categories', 'passengers', 'fecha_entrega', 'fecha_devolucion', 'sliderDesktop', 'sliderMobile'));
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
            'city'         => 'nullable|string|max:100',
            'mileage'      => 'nullable|integer|min:0',
            'next_verification' => 'nullable|date',
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
        $vehicle = Vehicle::with(['category', 'images'])->findOrFail($id);
    }

    public function detalle($id)
    {
        $vehicle = Vehicle::with('category')->where('active', 1)->findOrFail($id);
        return view('catalogo.detalle', compact('vehicle'));
        $vehicle = Vehicle::with(['category', 'images'])->findOrFail($id);
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
            'city'              => 'nullable|string|max:100',
            'mileage'           => 'nullable|integer|min:0',
            'next_verification' => 'nullable|date',
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

        $vehicle = Vehicle::with('images')->findOrFail($id);
    }

    public function destroy($id)
    {
        $vehicle     = Vehicle::findOrFail($id);
        $vehicleName = $vehicle->name;
        $vehicle->delete();

        return redirect()->route('vehiculos.index')
            ->with('success', "Vehículo '{$vehicleName}' eliminado exitosamente");
    }

    // Subir fotos de galería
    public function uploadImages(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'images'   => 'max:5',
        ]);

        // Verificar que no supere 5 fotos en total
        $totalActual = $vehicle->images()->count();
        $nuevas      = count($request->file('images') ?? []);

        if ($totalActual + $nuevas > 5) {
            return back()->with('error', "Solo puedes tener máximo 5 fotos. Ya tienes {$totalActual}.");
        }

        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . rand(1000, 9999) . '.webp';
            $imagePath = 'vehicles/gallery/' . $imageName;

            $manager = new ImageManager(new Driver());
            $img     = $manager->read($image->getRealPath());
            $img->cover(800, 500);

            Storage::disk('public')->put($imagePath, $img->toWebp(80)->toString());

            $vehicle->images()->create([
                'image_path' => $imagePath,
                'orden'      => $totalActual + $nuevas,
            ]);
        }

        return back()->with('success', 'Fotos agregadas correctamente.');
    }

    // Eliminar foto de galería
    public function deleteImage($id)
    {
        $image = \App\Models\VehicleImage::findOrFail($id);

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Foto eliminada correctamente.');
    }
}