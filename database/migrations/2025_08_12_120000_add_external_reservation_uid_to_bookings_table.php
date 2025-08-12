<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'external_reservation_uid')) {
                $table->string('external_reservation_uid')->nullable()->after('external_reservation_id');
                $table->index('external_reservation_uid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'external_reservation_uid')) {
                $table->dropIndex(['external_reservation_uid']);
                $table->dropColumn('external_reservation_uid');
            }
        });
    }
};

