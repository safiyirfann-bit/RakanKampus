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
        Schema::table('users', function (Blueprint $table) {
            // Durable "last login" history. "Currently online" / "last
            // active just now" is read live from the sessions table
            // instead (SESSION_DRIVER=database already keeps a fresh
            // last_activity per session row), so no separate column is
            // needed for that.
            $table->timestamp('last_login_at')->nullable()->after('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
    }
};
