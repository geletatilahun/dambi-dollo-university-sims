<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Campus Classrooms & Lecture Halls
        if (!Schema::hasTable('classrooms')) {
            Schema::create('classrooms', function (Blueprint $table) {
                $table->id();
                $table->string('building');
                $table->string('room_number', 50);
                $table->unsignedSmallInteger('capacity');
                $table->timestamps();
                $table->unique(['building', 'room_number']);
            });
        }

        // 2. Scheduled Lecture Timetables
        if (!Schema::hasTable('timetables')) {
            Schema::create('timetables', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_offering_id')->constrained()->cascadeOnDelete();
                $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
                $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
                $table->time('start_time');
                $table->time('end_time');
                $table->timestamps();
                $table->index(['classroom_id', 'day_of_week', 'start_time', 'end_time']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('classrooms');
    }
};