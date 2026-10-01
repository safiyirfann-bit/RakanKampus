<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The same 6-digit email codes now serve two jobs: password reset and
// confirming a new student's email at sign-up.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_otps', function (Blueprint $table) {
            $table->string('purpose', 20)->default('reset')->after('email');
            $table->index(['email', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::table('password_otps', function (Blueprint $table) {
            $table->dropIndex(['email', 'purpose']);
            $table->dropColumn('purpose');
        });
    }
};
