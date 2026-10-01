<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// One-off programmes in the timetable (camp, workshop, orientation week...):
// unlike class_schedules they happen once, on a date range, not every week.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 120);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('all_day')->default(true);
            $table->string('start_time', 5)->nullable();
            $table->string('end_time', 5)->nullable();
            $table->string('place', 120)->nullable();
            $table->string('color', 16)->default('amber');
            $table->timestamps();
            $table->index(['user_id', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
