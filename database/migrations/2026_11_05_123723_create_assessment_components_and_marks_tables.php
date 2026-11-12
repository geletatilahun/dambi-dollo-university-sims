<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Assessment Components for a Course Offering (e.g. Midterm 30%, Final 50%)
        if (!Schema::hasTable('assessment_components')) {
            Schema::create('assessment_components', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_offering_id')->constrained()->cascadeOnDelete();
                $table->string('title', 100); // e.g. Assignment, Quiz, Midterm, Final Exam
                $table->decimal('max_weight', 5, 2); // e.g. 15.00, 25.00, 50.00
                $table->timestamps();
            });
        }

        // 2. Student Marks per Assessment Component
        if (!Schema::hasTable('student_assessment_marks')) {
            Schema::create('student_assessment_marks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_component_id')->constrained()->cascadeOnDelete();
                $table->foreignId('registration_course_id')->constrained()->cascadeOnDelete();
                $table->decimal('score', 5, 2);
                $table->timestamps();
                $table->unique(['assessment_component_id', 'registration_course_id'], 'comp_reg_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_assessment_marks');
        Schema::dropIfExists('assessment_components');
    }
};