<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** "Report a problem" from Help & Support lands in the same admin Inbox as feedback. */
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->string('student_id')->nullable();
            $table->string('issue_type')->nullable();
            $table->text('issue_report')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropColumn(['student_id', 'issue_type', 'issue_report']);
        });
    }
};
