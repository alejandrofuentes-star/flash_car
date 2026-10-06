<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Total general = costo_total − (total_dias × costo por día del auto) − descuento
        // (lo calcula Renta::calcularTotalGeneral() al guardar)
        Schema::table('rentas', function (Blueprint $table) {
            if (!Schema::hasColumn('rentas', 'total_general')) {
                $table->decimal('total_general', 10, 2)->nullable()->after('descuento');
            }
        });

        // Rentas existentes: costo por día de la renta o, si no tiene, el del vehículo
        DB::statement("
            UPDATE rentas r
            LEFT JOIN vehicles v ON v.id = r.vehicle_id
            SET r.total_general = ROUND(
                COALESCE(r.costo_total, 0)
                - COALESCE(r.total_dias, 0) * COALESCE(r.costo_por_dia, v.costo_por_dia, 0)
                - COALESCE(r.descuento, 0)
            , 2)
        ");
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->dropColumn('total_general');
        });
    }
};
