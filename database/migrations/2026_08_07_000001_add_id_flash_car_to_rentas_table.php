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
            $table->unsignedBigInteger('id_flash_car')->nullable()->unique()->after('id');
        });

        $ultimaRenta = DB::table('rentas')->orderByDesc('id')->first();
        if ($ultimaRenta) {
            DB::table('rentas')->where('id', $ultimaRenta->id)->update(['id_flash_car' => 66625]);
        }
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->dropColumn('id_flash_car');
        });
    }
};
