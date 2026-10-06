<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Category;
use App\Models\State;
use App\Models\SiteStat;
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
        $categories   = Category::withCount('vehicles')->orderBy('name', 'asc')->get();
        $sinCategoria = Vehicle::whereNull('category_id')->count();
        return view('vehiculos.index', compact('categories', 'sinCategoria'));
    }

    // Lista de vehículos de una categoría ('sin-categoria' = vehículos cuya categoría fue eliminada)
    public function porCategoria($id)
    {
        $category = $id === 'sin-categoria' ? null : Category::findOrFail($id);

        $vehicles = Vehicle::with('rentasEnCurso')
            ->where('category_id', $category?->id)
            ->orderBy('name', 'asc')
            ->get();

        return view('vehiculos.por_categoria', compact('category', 'vehicles'));
    }

    // Regresa a la lista de vehículos de la categoría correspondiente
    private function redirectCategoria($categoryId)
    {
        return redirect()->route('vehiculos.categoria', $categoryId ?? 'sin-categoria');
    }

    public function catalogo()
    {
        // Un auto disponible por categoría (sin renta en curso en este momento)
        $vehicles = $this->autosDelCatalogo(
            Vehicle::with('category')->disponibleAhora()->orderBy('name', 'asc')->get()
        );

        $cities = State::whereHas('vehicles', fn($q) => $q->where('active', 1))
                        ->orderBy('name')
                        ->pluck('name');

        // Filtros basados solo en las tarjetas que se muestran
        $categories = $vehicles->pluck('category')->sortBy('name')->values();
        $passengers = $vehicles->pluck('passengers')->unique()->sort()->values();

        $sliderDesktop      = SliderImage::desktop()->active()->orderBy('orden')->get();
        $sliderMobile       = SliderImage::mobile()->active()->orderBy('orden')->get();
        $totalReservaciones = SiteStat::get('total_reservations');

        return view('catalogo.index', compact('vehicles', 'cities', 'categories', 'passengers', 'sliderDesktop', 'sliderMobile', 'totalReservaciones'));
    }

    public function buscar(Request $request)
    {
        $city            = $request->city;
        $fecha_entrega   = $request->fecha_entrega;
        $fecha_devolucion = $request->fecha_devolucion;


        $vehicles = Vehicle::with('category')
            ->where('active', 1)
            ->where('available', 1)
            ->when($city, fn($q) => $q->whereHas('states', fn($r) => $r->where('name', $city)))
            // Excluir autos que ya tienen renta en ese rango de fechas
            ->when($fecha_entrega && $fecha_devolucion, function($q) use ($fecha_entrega, $fecha_devolucion) {
                $q->whereDoesntHave('rentas', function($r) use ($fecha_entrega, $fecha_devolucion) {
                    $r->whereIn('estado', Vehicle::ESTADOS_OCUPAN)
                    ->where('fecha_entrega', '<=', $fecha_devolucion)
                    ->where('fecha_devolucion', '>=', $fecha_entrega);
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        // Un auto disponible por categoría entre los que cumplen la búsqueda
        $vehicles = $this->autosDelCatalogo($vehicles);

        $cities = State::whereHas('vehicles', fn($q) => $q->where('active', 1))
            ->orderBy('name')
            ->pluck('name');

        // Filtros basados solo en las tarjetas que se muestran
        $categories = $vehicles->pluck('category')->sortBy('name')->values();
        $passengers = $vehicles->pluck('passengers')->unique()->sort()->values();

        $sliderDesktop      = SliderImage::desktop()->active()->orderBy('orden')->get();
        $sliderMobile       = SliderImage::mobile()->active()->orderBy('orden')->get();
        $totalReservaciones = SiteStat::get('total_reservations');

        return view('catalogo.index', compact('vehicles', 'cities', 'city', 'categories', 'passengers', 'fecha_entrega', 'fecha_devolucion', 'sliderDesktop', 'sliderMobile', 'totalReservaciones'));
    }

    // Catálogo público: por cada categoría activa se muestran los autos marcados con
    // "mostrar en catálogo"; si ninguno de los marcados está disponible, se muestra el
    // primero (por nombre) de esa categoría para que la categoría no desaparezca.
    // $vehicles ya viene filtrado a los autos disponibles y ordenado por nombre.
    private function autosDelCatalogo($vehicles)
    {
        return $vehicles
            ->filter(fn($v) => $v->category && $v->category->active)
            ->groupBy('category_id')
            ->flatMap(function ($autos) {
                $marcados = $autos->where('mostrar_en_catalogo', true);
                return $marcados->isNotEmpty() ? $marcados : $autos->take(1);
            })
            ->sortBy('name')
            ->values();
    }

    // Marca/desmarca el auto para mostrarse en el catálogo público (checkbox en las tarjetas)
    public function toggleCatalogo($id)
    {
        $vehicle = Vehicle::with('rentasEnCurso')->findOrFail($id);
        if (!$vehicle->disponible_ahora) {
            return back()->with('error', "'{$vehicle->name}' no está disponible; no se puede cambiar su visibilidad en el catálogo");
        }
        $vehicle->update(['mostrar_en_catalogo' => !$vehicle->mostrar_en_catalogo]);

        return back()->with('success', $vehicle->mostrar_en_catalogo
            ? "'{$vehicle->name}' se mostrará en el catálogo"
            : "'{$vehicle->name}' ya no se mostrará en el catálogo");
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name', 'asc')->get();
        $states     = State::where('active', 1)->orderBy('name')->get();
        return view('vehiculos.create', compact('categories', 'states'));
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
            'proveedor'    => 'nullable|string|max:255',
            'fecha_pago_proveedor' => 'nullable|date',
            'costo_por_dia'        => 'nullable|numeric|min:0',
            'transmission' => 'required|in:manual,automatic',
            'available'    => 'boolean',
            'active'       => 'boolean',
            'mileage'      => 'nullable|integer|min:0',
            'next_verification' => 'nullable|date',
            'state_id'     => 'nullable|exists:states,id',
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
        $validated['mostrar_en_catalogo'] = $request->has('mostrar_en_catalogo');

        // Un vehículo opera en una sola ciudad (se sigue guardando en la pivot state_vehicle)
        $stateIds = !empty($validated['state_id']) ? [$validated['state_id']] : [];
        unset($validated['state_id']);
        unset($validated['motivo_inactivo']);

        $vehicle = Vehicle::create($validated);
        $vehicle->states()->sync($stateIds);

        return $this->redirectCategoria($vehicle->category_id)
            ->with('success', "Vehículo '{$vehicle->name}' creado exitosamente");
    }

    public function show($id)
    {
        $vehicle = Vehicle::with(['category', 'images', 'states', 'rentasEnCurso'])->findOrFail($id);
        return view('vehiculos.detalles', compact('vehicle'));
    }

    public function detalle($id)
    {
        $vehicle = Vehicle::with('category')->where('active', 1)->findOrFail($id);
        return view('catalogo.detalle', compact('vehicle'));
        $vehicle = Vehicle::with(['category', 'images'])->findOrFail($id);
    }

    public function edit($id)
    {
        $vehicle    = Vehicle::with('states')->findOrFail($id);
        $categories = Category::active()->orderBy('name', 'asc')->get();
        $states     = State::where('active', 1)->orderBy('name')->get();
        return view('vehiculos.editar_vehiculo', compact('vehicle', 'categories', 'states'));
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
            'proveedor'    => 'nullable|string|max:255',
            'fecha_pago_proveedor' => 'nullable|date',
            'costo_por_dia'        => 'nullable|numeric|min:0',
            'transmission' => 'required|in:manual,automatic',
            'available'    => 'boolean',
            'active'       => 'boolean',
            'mileage'           => 'nullable|integer|min:0',
            'next_verification' => 'nullable|date',
            'motivo_inactivo'   => 'nullable|in:Servicio,Mtto Mayor,Traslado',
            'state_id'     => 'nullable|exists:states,id',
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
        $validated['mostrar_en_catalogo'] = $request->has('mostrar_en_catalogo');
        $validated['motivo_inactivo'] = $validated['active'] ? null : ($request->motivo_inactivo ?? null);

        // Un vehículo opera en una sola ciudad (se sigue guardando en la pivot state_vehicle)
        $stateIds = !empty($validated['state_id']) ? [$validated['state_id']] : [];
        unset($validated['state_id']);

        $vehicle->update($validated);
        $vehicle->states()->sync($stateIds);

        return $this->redirectCategoria($vehicle->category_id)
            ->with('success', 'Vehículo actualizado exitosamente');
    }

    public function destroy($id)
    {
        $vehicle     = Vehicle::findOrFail($id);
        $vehicleName = $vehicle->name;
        $categoryId  = $vehicle->category_id;
        $vehicle->delete();

        return $this->redirectCategoria($categoryId)
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