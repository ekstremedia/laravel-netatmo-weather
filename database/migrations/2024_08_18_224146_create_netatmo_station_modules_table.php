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
        Schema::create('netatmo_modules', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('netatmo_station_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('module_id')->unique();
            $table->string('module_name');
            $table->string('type');
            $table->string('battery_percent')->nullable();
            $table->string('battery_vp')->nullable();
            $table->string('firmware')->nullable();
            $table->string('last_message')->nullable();
            $table->string('last_seen')->nullable();
            $table->string('wifi_status')->nullable();
            $table->string('rf_status')->nullable();
            $table->string('reachable')->nullable();
            $table->string('last_status_store')->nullable();
            $table->string('date_setup')->nullable();
            $table->string('last_setup')->nullable();
            $table->string('co2_calibrating')->nullable();
            $table->string('home_id')->nullable();
            $table->string('home_name')->nullable();
            $table->json('user')->nullable();
            $table->json('place')->nullable();
            $table->json('data_type'); // Stores the types of data the module collects, e.g., ["Temperature", "Humidity"]
            $table->json('dashboard_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('netatmo_modules');
    }
};
