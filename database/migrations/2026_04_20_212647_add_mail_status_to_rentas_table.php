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
        Schema::table('rentas', function (Blueprint $table) {
            $table->boolean('mail_enviado')->default(false)->after('estado');
            $table->timestamp('mail_enviado_at')->nullable()->after('mail_enviado');
        });
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->dropColumn(['mail_enviado', 'mail_enviado_at']);
        });
    }
};
