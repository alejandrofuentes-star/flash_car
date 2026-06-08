<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')->insert([
            [
                'key'        => 'anticipo_tipo',
                'value'      => 'fijo',
                'label'      => 'Tipo de anticipo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'anticipo_monto',
                'value'      => '0',
                'label'      => 'Monto de anticipo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', ['anticipo_tipo', 'anticipo_monto'])->delete();
    }
};
