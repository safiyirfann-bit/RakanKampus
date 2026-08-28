<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('information_id')
                  ->constrained('information')
                  ->onDelete('cascade');
            $table->string('intent');
            $table->string('question');
            $table->text('answer');
            $table->string('category')->nullable();
            $table->string('keywords')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_bases');
    }
};