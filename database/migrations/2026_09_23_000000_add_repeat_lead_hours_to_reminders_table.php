<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            // Extra lead times (in hours before due_at) to notify at, on top of the
            // main lead_hours notification — lets a reminder "remind me many times".
            $table->json('repeat_lead_hours')->nullable()->after('lead_hours');

            // Tracks which of the repeat_lead_hours have already fired, so the
            // scheduler doesn't re-notify for the same lead time.
            $table->json('notified_leads')->nullable()->after('notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn(['repeat_lead_hours', 'notified_leads']);
        });
    }
};
