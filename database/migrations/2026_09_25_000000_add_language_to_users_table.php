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
            // Student's preferred UI language: en (English), ms (Bahasa
            // Melayu), zh (Mandarin), ta (Tamil). Read by the SetLocale
            // middleware on every request to translate student-facing pages.
            $table->string('language', 5)->default('en')->after('notification_settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('language');
        });
    }
};
