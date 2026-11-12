<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users & RBAC
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        // 2. Academic Structure
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')->constrained()->restrictOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->enum('degree_level', ['Undergraduate', 'Postgraduate', 'PhD', 'Diploma']);
            $table->enum('study_mode', ['Regular', 'Extension', 'Weekend', 'Summer']);
            $table->unsignedTinyInteger('duration_years')->default(4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('registration_start');
            $table->date('registration_deadline');
            $table->date('add_drop_deadline');
            $table->date('grade_submission_deadline');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->string('code', 20)->unique();
            $table->string('title');
            $table->decimal('credit_hours', 4, 1);
            $table->decimal('ects', 4, 1)->default(0.0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('curricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('version', 20);
            $table->string('title');
            $table->decimal('total_credits_required', 5, 1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('curriculum_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('year_level');
            $table->unsignedTinyInteger('semester_term');
            $table->timestamps();
        });

        // 3. Students
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->foreignId('curriculum_id')->constrained()->restrictOnDelete();
            $table->string('student_id_number', 50)->unique();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->enum('gender', ['Male', 'Female']);
            $table->date('date_of_birth');
            $table->year('cohort_year');
            $table->enum('study_mode', ['Regular', 'Extension', 'Weekend', 'Summer'])->default('Regular');
            $table->enum('status', ['Admitted', 'Active', 'LeaveOfAbsence', 'Suspended', 'Withdrawn', 'Dismissed', 'Graduated'])->default('Active');
            $table->timestamps();
        });

        // 4. Offerings & Registrations
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->string('section', 20);
            $table->unsignedSmallInteger('capacity')->default(60);
            $table->timestamps();
        });

        Schema::create('course_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_offering_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['Draft', 'Submitted', 'Approved', 'Rejected'])->default('Draft');
            $table->decimal('total_registered_credits', 4, 1)->default(0.0);
            $table->timestamps();
        });

        Schema::create('registration_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_registration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_offering_id')->constrained()->restrictOnDelete();
            $table->enum('item_status', ['Registered', 'Dropped'])->default('Registered');
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('grade_letter', 5)->nullable();
            $table->decimal('grade_points', 4, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('grade_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_offering_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('status', ['Draft', 'Submitted', 'DepartmentEndorsed', 'Published'])->default('Draft');
            $table->timestamps();
        });

        Schema::create('semester_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->decimal('sgpa', 4, 2)->default(0.00);
            $table->decimal('cgpa', 4, 2)->default(0.00);
            $table->string('academic_standing', 50)->default('Good Standing');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_results');
        Schema::dropIfExists('grade_sheets');
        Schema::dropIfExists('registration_courses');
        Schema::dropIfExists('course_registrations');
        Schema::dropIfExists('course_instructors');
        Schema::dropIfExists('course_offerings');
        Schema::dropIfExists('students');
        Schema::dropIfExists('curriculum_courses');
        Schema::dropIfExists('curricula');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('colleges');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
    }
};