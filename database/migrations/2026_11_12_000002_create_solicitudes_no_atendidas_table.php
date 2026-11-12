<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solicitudes de renta rechazadas por falta de auto libre en las fechas pedidas
        // (nunca llegan a ser una Renta). Las llena RentaController::registrarNoAtendida().
        if (!Schema::hasTable('solicitudes_no_atendidas')) {
            Schema::create('solicitudes_no_atendidas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
                $table->date('fecha_entrega');
                $table->string('hora_entrega', 5)->nullable();
                $table->date('fecha_devolucion');
                $table->string('hora_devolucion', 5)->nullable();
                $table->unsignedInteger('total_dias')->default(1);
                $table->string('nombre_completo', 150)->nullable();
                $table->string('correo', 100)->nullable();
                $table->string('origen', 30);
                $table->unsignedInteger('intentos')->default(1);
                $table->timestamps();

                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_no_atendidas');
    }
};
