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
        Schema::table('netatmo_modules', static function (Blueprint $table) {
            // Drop the global unique constraint on module_id
            $table->dropUnique(['module_id']);

            // Add composite unique constraint on station_id + module_id
            // This allows the same module_id to exist for different stations
            $table->unique(['netatmo_station_id', 'module_id'], 'netatmo_modules_station_module_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('netatmo_modules', static function (Blueprint $table) {
            // Remove composite unique constraint
            $table->dropUnique('netatmo_modules_station_module_unique');

            // Restore global unique constraint (this might fail if there are duplicates)
            $table->unique('module_id');
        });
    }
};
