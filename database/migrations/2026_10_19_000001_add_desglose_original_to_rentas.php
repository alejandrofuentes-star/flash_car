<?php

use App\Models\Renta;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Foto fija del desglose al generar la renta (la llena Renta::armarDesgloseOriginal() al crear)
        Schema::table('rentas', function (Blueprint $table) {
            if (!Schema::hasColumn('rentas', 'desglose_original')) {
                $table->json('desglose_original')->nullable()->after('total_general');
            }
        });

        // Rentas existentes: se arma con sus datos actuales (pudieron editarse antes), marcado como reconstruido
        Renta::with('vehicle.category')->whereNull('desglose_original')->chunkById(200, function ($rentas) {
            foreach ($rentas as $renta) {
                $foto = $renta->armarDesgloseOriginal();
                if ($foto) {
                    $foto['reconstruido'] = true;
                    // Por query builder para no mover updated_at ni disparar eventos
                    DB::table('rentas')->where('id', $renta->id)->update(['desglose_original' => json_encode($foto)]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->dropColumn('desglose_original');
        });
    }
};
