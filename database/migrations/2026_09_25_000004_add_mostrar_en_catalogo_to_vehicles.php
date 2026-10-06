<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'mostrar_en_catalogo')) {
                $table->boolean('mostrar_en_catalogo')->default(false)->after('available');
            }
        });

        // Por default se marca un auto por categoría: el primero (por nombre) activo y disponible
        $ids = DB::table('vehicles')
            ->whereNotNull('category_id')
            ->where('active', true)
            ->where('available', true)
            ->orderBy('name')
            ->get(['id', 'category_id'])
            ->unique('category_id')
            ->pluck('id');

        DB::table('vehicles')->whereIn('id', $ids)->update(['mostrar_en_catalogo' => true]);
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('mostrar_en_catalogo');
        });
    }
};
