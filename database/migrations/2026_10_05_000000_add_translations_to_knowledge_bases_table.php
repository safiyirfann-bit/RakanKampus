<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The question of each knowledge-base entry in the app's 4 languages.
 * `question` stays the main (original) text and the answer is kept once;
 * the AI replies in the student's language using that one answer.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->string('question_ms')->nullable()->after('question'); // only when `question` isn't Malay
            $table->string('question_en')->nullable()->after('question_ms');
            $table->string('question_zh')->nullable()->after('question_en');
            $table->string('question_ta')->nullable()->after('question_zh');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->dropColumn(['question_ms', 'question_en', 'question_zh', 'question_ta']);
        });
    }
};
