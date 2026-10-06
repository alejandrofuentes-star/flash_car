<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Precios de la categoría con los que se calculó costo_total (las rentas anteriores quedan en null)
        Schema::table('rentas', function (Blueprint $table) {
            if (!Schema::hasColumn('rentas', 'precio_dia')) {
                $table->decimal('precio_dia', 10, 2)->nullable()->after('costo_total');
            }
            if (!Schema::hasColumn('rentas', 'precio_semana')) {
                $table->decimal('precio_semana', 10, 2)->nullable()->after('precio_dia');
            }
            if (!Schema::hasColumn('rentas', 'precio_mes')) {
                $table->decimal('precio_mes', 10, 2)->nullable()->after('precio_semana');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->dropColumn(['precio_dia', 'precio_semana', 'precio_mes']);
        });
    }
};
