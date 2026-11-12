<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Motivo de cancelación (Renta::MOTIVOS_CANCELACION). Distingue las canceladas por falta de
        // disponibilidad (cuentan como demanda no atendida en reportes) de las demás.
        Schema::table('rentas', function (Blueprint $table) {
            if (!Schema::hasColumn('rentas', 'motivo_cancelacion')) {
                $table->string('motivo_cancelacion', 40)->nullable()->after('estado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            if (Schema::hasColumn('rentas', 'motivo_cancelacion')) {
                $table->dropColumn('motivo_cancelacion');
            }
        });
    }
};
