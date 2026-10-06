<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notificaciones_leidas')) {
            return;
        }

        Schema::create('notificaciones_leidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Identifica la alerta, ej. "pago_proveedor:15:2026-10-01"
            $table->string('clave', 150);
            $table->timestamps();

            $table->unique(['user_id', 'clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones_leidas');
    }
};
