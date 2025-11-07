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
        Schema::table('netatmo_stations', static function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('station_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('netatmo_stations', static function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }
};
