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
        Schema::table('netatmo_module_readings', function (Blueprint $table) {
            // Add index on time_utc for faster time-based queries
            $table->index('time_utc');

            // Add composite index for module + time queries
            $table->index(['netatmo_module_id', 'time_utc']);

            // Add unique constraint to prevent duplicate readings
            $table->unique(['netatmo_module_id', 'time_utc']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('netatmo_module_readings', function (Blueprint $table) {
            $table->dropUnique(['netatmo_module_id', 'time_utc']);
            $table->dropIndex(['netatmo_module_id', 'time_utc']);
            $table->dropIndex(['time_utc']);
        });
    }
};
