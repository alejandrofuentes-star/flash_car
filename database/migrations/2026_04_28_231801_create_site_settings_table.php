<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->string('label');
            $table->timestamps();
        });

        DB::table('site_settings')->insert([
            ['key' => 'telefono', 'label' => 'Teléfono', 'value' => '1122334455', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'whatsapp', 'label' => 'WhatsApp', 'value' => '1122334455', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
