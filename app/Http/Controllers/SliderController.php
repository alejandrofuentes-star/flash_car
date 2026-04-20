<?php

namespace App\Http\Controllers;

use App\Models\SliderImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SliderController extends Controller
{
    public function index()
    {
        $desktop = SliderImage::desktop()->orderBy('orden')->get();
        $mobile  = SliderImage::mobile()->orderBy('orden')->get();

        return view('slider.index', compact('desktop', 'mobile'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'   => 'required|in:desktop,mobile',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $type  = $request->type;
        $total = SliderImage::where('type', $type)->count();

        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . rand(1000, 9999) . '.webp';
            $imagePath = 'slider/' . $type . '/' . $imageName;

            $manager = new ImageManager(new Driver());
            $img     = $manager->read($image->getRealPath());

            if ($type === 'desktop') {
                $img->cover(2000, 1100);
            } else {
                $img->cover(1200, 2000);
            }

            Storage::disk('public')->put($imagePath, $img->toWebp(85)->toString());

            SliderImage::create([
                'type'       => $type,
                'image_path' => $imagePath,
                'orden'      => $total++,
                'active'     => true,
            ]);
        }

        return back()->with('success', 'Imagen(es) del slider subidas correctamente.');
    }

    public function toggle($id)
    {
        $image = SliderImage::findOrFail($id);
        $image->update(['active' => !$image->active]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['orden' => 'required|array']);

        foreach ($request->orden as $id => $orden) {
            SliderImage::where('id', $id)->update(['orden' => $orden]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $image = SliderImage::findOrFail($id);

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Imagen eliminada del slider.');
    }
}
