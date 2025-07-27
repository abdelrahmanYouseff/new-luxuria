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
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('pricing_type', ['daily', 'weekly', 'monthly'])->default('daily')->after('daily_rate');
            $table->decimal('applied_rate', 8, 2)->after('pricing_type')->comment('Actual rate per day used for calculation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['pricing_type', 'applied_rate']);
        });
    }
};
