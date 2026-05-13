<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')->insertOrIgnore([
            'key'   => 'admin_notification_email',
            'value' => 'flashcarental@gmail.com',
            'label' => 'Correo de notificaciones',
        ]);
    }

    public function down(): void
    {
        DB::table('site_settings')->where('key', 'admin_notification_email')->delete();
    }
};
