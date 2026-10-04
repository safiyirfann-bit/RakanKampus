<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Profile cover picture (PC profile page), stored as a JPEG data URI like photo_data
            // because Render's free plan has no persistent disk. NULL = the default campus artwork.
            $table->longText('cover_data')->nullable()->after('photo_data');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cover_data');
        });
    }
};
