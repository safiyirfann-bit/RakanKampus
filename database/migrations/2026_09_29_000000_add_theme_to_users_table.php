<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Student's colour theme: light, dark, or system (follow the phone/PC). */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('theme', 10)->default('system');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('theme');
        });
    }
};
