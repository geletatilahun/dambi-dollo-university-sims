<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Program Curricula (if not existing)
        if (!Schema::hasTable('curricula')) {
            Schema::create('curricula', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_id')->constrained()->cascadeOnDelete();
                $table->string('version', 20); // e.g. 2026-Harmonized
                $table->string('title');
                $table->decimal('total_credits_required', 5, 1)->default(140.0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Mapping Courses into Curricular Roadmap (Year Level & Semester Term)
        if (!Schema::hasTable('curriculum_courses')) {
            Schema::create('curriculum_courses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curriculum_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_id')->constrained()->restrictOnDelete();
                $table->unsignedTinyInteger('year_level'); // 1, 2, 3, 4
                $table->unsignedTinyInteger('semester_term'); // 1, 2
                $table->timestamps();
            });
        }

        // 3. Course Prerequisite Dependencies
        if (!Schema::hasTable('course_prerequisites')) {
            Schema::create('course_prerequisites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->foreignId('prerequisite_course_id')->constrained('courses')->restrictOnDelete();
                $table->timestamps();
                $table->unique(['course_id', 'prerequisite_course_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
        Schema::dropIfExists('curriculum_courses');
        Schema::dropIfExists('curricula');
    }
};