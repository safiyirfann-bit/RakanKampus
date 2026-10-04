<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            // "Notify me" alerts, in minutes before the class starts (max 3), e.g. [1080, 15].
            // NULL = never set -> the old default of one alert 15 minutes before.
            $table->json('notify_offsets')->nullable()->after('lecturer');
            // Which alerts already went out, as "Y-m-d|minutes" keys, so each fires once per class.
            $table->json('notified_keys')->nullable()->after('notify_offsets');
        });
    }

    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropColumn(['notify_offsets', 'notified_keys']);
        });
    }
};
