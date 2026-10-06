<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Costo por día del auto para esta renta (editable desde el detalle); null = se usa el del vehículo
        Schema::table('rentas', function (Blueprint $table) {
            if (!Schema::hasColumn('rentas', 'costo_por_dia')) {
                $table->decimal('costo_por_dia', 10, 2)->nullable()->after('precio_mes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->dropColumn('costo_por_dia');
        });
    }
};
