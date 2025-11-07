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
        Schema::create('netatmo_stations', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('station_name');
            $table->text('client_id');
            $table->text('client_secret');
            $table->string('redirect_uri')->nullable();
            $table->string('webhook_uri')->nullable();
            $table->uuid()->unique();
            $table->timestamps();

            // Optional: Create index for better performance
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('netatmo_stations');
    }
};
