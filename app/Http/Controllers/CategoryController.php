<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create()
    {
        return view('vehiculos.create_categoria');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:categories',
            'price_per_day'   => 'required|numeric|min:0',
            'price_per_week'  => 'required|numeric|min:0',
            'price_per_month' => 'required|numeric|min:0',
            'warranty'        => 'required|numeric|min:0',
            'image_url'       => 'nullable|url',
            'active'          => 'boolean',
        ]);

        $validated['active'] = $request->has('active');

        Category::create($validated);

        return redirect()->route('vehiculos.index')
            ->with('success', "Categoría '{$validated['name']}' creada exitosamente");
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return view('vehiculos.detalle_categoria', compact('category'));
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('vehiculos.editar_categoria', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:categories,name,' . $id,
            'price_per_day'   => 'required|numeric|min:0',
            'price_per_week'  => 'required|numeric|min:0',
            'price_per_month' => 'required|numeric|min:0',
            'warranty'        => 'required|numeric|min:0',
            'image_url'       => 'nullable|url',
            'active'          => 'boolean',
        ]);

        $validated['active'] = $request->has('active');

        $category->update($validated);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Categoría actualizada exitosamente');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $name = $category->name;
        $category->delete();

        return redirect()->route('vehiculos.index')
            ->with('success', "Categoría '{$name}' eliminada exitosamente");
    }
}
