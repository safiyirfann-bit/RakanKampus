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
        Schema::table('class_schedules', function (Blueprint $table) {
            // Tracks the calendar date this recurring weekly class last got a
            // "starting soon" push notification, so it fires once per real day
            // instead of once per week (the row itself never changes date).
            $table->date('last_notified_date')->nullable()->after('lecturer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropColumn('last_notified_date');
        });
    }
};
