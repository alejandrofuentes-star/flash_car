<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Monto de descuentos de la renta (editable desde el detalle)
        Schema::table('rentas', function (Blueprint $table) {
            if (!Schema::hasColumn('rentas', 'descuento')) {
                $table->decimal('descuento', 10, 2)->nullable()->after('costo_por_dia');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->dropColumn('descuento');
        });
    }
};
