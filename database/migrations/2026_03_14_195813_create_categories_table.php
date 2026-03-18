
<?php
// ============================================================
// 1. MIGRACIÓN - create_categories_table
// ============================================================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price_per_day', 10, 2);
            $table->decimal('price_per_week', 10, 2);
            $table->decimal('price_per_month', 10, 2);
            $table->decimal('warranty', 10, 2);
            $table->string('image_url')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};