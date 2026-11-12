<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Advisor-Student Assignment
        if (!Schema::hasTable('academic_advisor_assignments')) {
            Schema::create('academic_advisor_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('advisor_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_id')->unique()->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // 2. Advising Intervention Records
        if (!Schema::hasTable('advising_notes')) {
            Schema::create('advising_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->foreignId('advisor_user_id')->constrained('users');
                $table->string('intervention_type'); // e.g. Academic Warning Counseling, Course Load Guidance
                $table->text('notes');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('advising_notes');
        Schema::dropIfExists('academic_advisor_assignments');
    }
};