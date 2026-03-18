<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->string('nombre_completo');
            $table->string('telefono', 20);
            $table->string('correo', 100);
            $table->string('ciudad');
            $table->date('fecha_entrega');
            $table->time('hora_entrega');
            $table->string('lugar_entrega');
            $table->date('fecha_devolucion');
            $table->time('hora_devolucion');
            $table->string('lugar_devolucion');
            $table->integer('num_pasajeros');
            $table->integer('total_dias');
            $table->decimal('costo_total', 10, 2);
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'completada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rentas');
    }
};
