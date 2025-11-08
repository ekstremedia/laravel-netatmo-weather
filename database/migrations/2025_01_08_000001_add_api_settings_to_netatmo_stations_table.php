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
        Schema::table('netatmo_stations', function (Blueprint $table) {
            $table->boolean('api_enabled')->default(false)->after('is_public');
            $table->string('api_token', 64)->nullable()->after('api_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('netatmo_stations', function (Blueprint $table) {
            $table->dropColumn(['api_enabled', 'api_token']);
        });
    }
};
