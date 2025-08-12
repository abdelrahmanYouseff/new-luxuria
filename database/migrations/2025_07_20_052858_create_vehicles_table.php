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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id(); // auto-increment integer primary key
            $table->string('api_id')->nullable()->unique(); // لتخزين UUID من الـ API

            // API Data Fields
            // $table->string('api_id')->nullable(); // احذف هذا السطر نهائياً
            $table->string('plate_number')->nullable();
            $table->string('status')->default('available');
            $table->string('ownership_status')->default('owned');

            // Vehicle Info Fields
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('year')->nullable();
            $table->string('color')->nullable();
            $table->string('category')->default('economy');

            // Pricing Fields
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->decimal('weekly_rate', 10, 2)->nullable();
            $table->decimal('monthly_rate', 10, 2)->nullable();

            // Additional Fields
            $table->string('transmission')->default('Automatic');
            $table->integer('seats')->default(5);
            $table->integer('doors')->default(4);
            $table->integer('odometer')->default(0);
            $table->text('description')->nullable();

            // Image Field
            $table->string('image')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            // $table->index('api_id'); // احذف هذا السطر نهائياً
            $table->index('status');
            $table->index('category');
            $table->index('make');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
