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
        Schema::table('reservations', function (Blueprint $table) {
            // Drop the existing foreign key constraint to cars table
            $table->dropForeign(['car_id']);
            
            // Rename car_id to vehicle_id for clarity
            $table->renameColumn('car_id', 'vehicle_id');
        });
        
        // Add the new foreign key constraint to vehicles table
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop the foreign key constraint to vehicles table
            $table->dropForeign(['vehicle_id']);
            
            // Rename vehicle_id back to car_id
            $table->renameColumn('vehicle_id', 'car_id');
        });
        
        // Add back the foreign key constraint to cars table
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });
    }
};
