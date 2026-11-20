<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Un pago de Stripe genera una sola renta. Respaldo en BD de la revisión de RentaController::store()
        // (y del webhook) para que dos envíos simultáneos con el mismo pago no creen dos rentas.
        // Las rentas sin pago con tarjeta tienen null y no chocan entre sí.
        DB::table('rentas')->where('payment_intent_id', '')->update(['payment_intent_id' => null]);

        $duplicados = DB::table('rentas')
            ->whereNotNull('payment_intent_id')
            ->groupBy('payment_intent_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('payment_intent_id');

        if ($duplicados->isNotEmpty()) {
            throw new RuntimeException(
                'Hay rentas que comparten el mismo pago de Stripe; revísalas antes de correr esta migración: '
                . $duplicados->implode(', ')
            );
        }

        Schema::table('rentas', function (Blueprint $table) {
            $table->unique('payment_intent_id', 'rentas_payment_intent_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->dropUnique('rentas_payment_intent_id_unique');
        });
    }
};
