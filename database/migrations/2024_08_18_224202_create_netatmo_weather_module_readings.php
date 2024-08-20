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
        Schema::create('netatmo_module_readings', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('netatmo_module_id')
                ->constrained('netatmo_modules')
                ->onDelete('cascade');
            $table->timestamp('time_utc');
            $table->json('dashboard_data'); // Stores the actual sensor data like temperature, humidity, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('netatmo_module_readings');
    }
};
