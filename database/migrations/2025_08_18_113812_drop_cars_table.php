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
        Schema::dropIfExists('cars');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Create cars table back if needed (optional - you can leave this empty)
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->longText('specs')->nullable();
            $table->longText('availability')->nullable();
            $table->decimal('price_per_day', 10, 2)->default(0);
            $table->decimal('price_per_week', 10, 2)->default(0);
            $table->decimal('price_per_month', 10, 2)->default(0);
            $table->timestamps();
        });
    }
};
