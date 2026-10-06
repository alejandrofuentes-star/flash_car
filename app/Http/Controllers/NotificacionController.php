<?php

namespace App\Http\Controllers;

use App\Models\NotificacionLeida;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    // Días que se conservan las marcas de leído (las alertas viejas ya no se calculan)
    private const DIAS_CONSERVAR = 60;

    // Marca como leídas una o varias alertas de la campana (claves generadas en AppServiceProvider)
    public function marcarLeidas(Request $request)
    {
        $request->validate([
            'claves'   => 'required|array|min:1|max:100',
            'claves.*' => 'required|string|max:150',
        ]);

        $userId = $request->user()->id;
        $ahora  = now();

        NotificacionLeida::upsert(
            collect($request->claves)->unique()->map(fn ($clave) => [
                'user_id'    => $userId,
                'clave'      => $clave,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ])->values()->all(),
            ['user_id', 'clave'],
            ['updated_at']
        );

        NotificacionLeida::where('user_id', $userId)
            ->where('created_at', '<', $ahora->copy()->subDays(self::DIAS_CONSERVAR))
            ->delete();

        return response()->json(['ok' => true]);
    }
}
