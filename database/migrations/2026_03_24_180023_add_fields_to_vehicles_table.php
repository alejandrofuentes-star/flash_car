<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('vehicles', function (Blueprint $table) {
        $table->string('city')->nullable();
        $table->integer('mileage')->nullable();
        $table->date('next_verification')->nullable();
    });
}

public function down(): void
{
    Schema::table('vehicles', function (Blueprint $table) {
        $table->dropColumn(['city', 'mileage', 'next_verification']);
    });
}
};
