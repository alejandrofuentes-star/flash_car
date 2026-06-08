<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')->insert([
            [
                'key'        => 'asset_v_general',
                'value'      => '1.2',
                'label'      => 'Versión styles_general.css',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'asset_v_publico',
                'value'      => '1.7',
                'label'      => 'Versión styles_pagina_principal.css',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'asset_v_formulario',
                'value'      => '1.1',
                'label'      => 'Versión formulario_renta.js',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('site_settings')
            ->whereIn('key', ['asset_v_general', 'asset_v_publico', 'asset_v_formulario'])
            ->delete();
    }
};
