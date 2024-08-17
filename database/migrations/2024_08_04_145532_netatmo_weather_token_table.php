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
        Schema::create('netatmo_weather_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('netatmo_weather_station_id')
                ->constrained('netatmo_weather_stations')
                ->onDelete('cascade');
            $table->text('access_token');
            $table->text('refresh_token');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netatmo_tokens');
    }
};
