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
            $table->string('module_type');
            $table->json('data_type'); // Stores the types of data the module collects, e.g., ["Temperature", "Humidity"]
            //            $table->uuid()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('netatmo_modules');
    }
};
