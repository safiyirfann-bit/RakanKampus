<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional map pin for a knowledge-base entry (e.g. "Perpustakaan kat mana?").
 * When the matched entry has coordinates, the chat shows a small map + "Tunjuk Arah" button.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->string('location_name')->nullable()->after('keywords');
            $table->decimal('latitude', 10, 7)->nullable()->after('location_name');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->dropColumn(['location_name', 'latitude', 'longitude']);
        });
    }
};
