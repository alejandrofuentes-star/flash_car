<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::orderBy('label')->get();
        return view('settings.index', compact('settings'));
    }

    public function update(\Illuminate\Http\Request $request)
    {
        foreach ($request->settings as $key => $value) {
            SiteSetting::set($key, $value ?? '');
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}
