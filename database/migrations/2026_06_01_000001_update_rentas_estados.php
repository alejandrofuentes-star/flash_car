<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->string('estado')->default('reserva_confirmada')->change();
        });

        DB::table('rentas')->whereIn('estado', ['pendiente', 'confirmada'])->update(['estado' => 'reserva_confirmada']);
        DB::table('rentas')->where('estado', 'completada')->update(['estado' => 'contrato_finalizado']);
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->string('estado')->default('pendiente')->change();
        });

        DB::table('rentas')->where('estado', 'reserva_confirmada')->update(['estado' => 'pendiente']);
        DB::table('rentas')->where('estado', 'contrato_finalizado')->update(['estado' => 'completada']);
    }
};
