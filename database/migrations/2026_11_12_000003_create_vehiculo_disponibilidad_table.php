<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Historial de disponibilidad de la flota: un registro cada vez que un vehículo se da de alta,
        // cambia active/available o se elimina (eventos de Vehicle). El estado de un auto en una fecha
        // es el último registro con desde <= esa fecha. Sin llave foránea a propósito: si el vehículo
        // se elimina, su historial se conserva para no alterar la capacidad de meses pasados.
        if (!Schema::hasTable('vehiculo_disponibilidad')) {
            Schema::create('vehiculo_disponibilidad', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vehicle_id');
                $table->boolean('active');
                $table->boolean('available');
                $table->dateTime('desde');
                $table->boolean('reconstruido')->default(false);
                $table->timestamp('created_at')->nullable();

                $table->index(['vehicle_id', 'desde']);
            });
        }

        // Vehículos existentes: no hay historial previo, así que se toma su estado actual desde la
        // fecha en que se registraron, o desde su primera renta si es anterior (aproximación,
        // marcada como reconstruida).
        if (DB::table('vehiculo_disponibilidad')->doesntExist()) {
            $primeraRenta = DB::table('rentas')
                ->selectRaw('vehicle_id, MIN(fecha_entrega) as primera')
                ->groupBy('vehicle_id')
                ->pluck('primera', 'vehicle_id');

            DB::table('vehicles')->orderBy('id')->get(['id', 'active', 'available', 'created_at'])
                ->chunk(200)
                ->each(function ($vehiculos) use ($primeraRenta) {
                    DB::table('vehiculo_disponibilidad')->insert($vehiculos->map(function ($v) use ($primeraRenta) {
                        $desde = collect([$v->created_at, isset($primeraRenta[$v->id]) ? $primeraRenta[$v->id] . ' 00:00:00' : null])
                            ->filter()
                            ->min() ?? now();

                        return [
                            'vehicle_id'   => $v->id,
                            'active'       => (bool) $v->active,
                            'available'    => (bool) $v->available,
                            'desde'        => $desde,
                            'reconstruido' => true,
                            'created_at'   => now(),
                        ];
                    })->all());
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculo_disponibilidad');
    }
};
