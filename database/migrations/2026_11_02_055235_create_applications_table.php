<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number', 30)->unique(); // e.g. APP-2026-0001
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            
            // Applicant Bio
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->enum('gender', ['Male', 'Female']);
            $table->date('date_of_birth');
            $table->string('national_id')->nullable()->index();
            $table->string('email')->unique();
            $table->string('phone_number', 30);
            
            // Academic Choices
            $table->enum('study_mode', ['Regular', 'Extension', 'Weekend', 'Summer'])->default('Regular');
            $table->enum('entry_type', ['Freshman', 'AdvancedStanding', 'Transfer'])->default('Freshman');
            
            // Application Lifecycle
            $table->enum('status', ['Submitted', 'UnderReview', 'Accepted', 'Rejected', 'Enrolled'])->default('Submitted');
            $table->text('reviewer_remarks')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            
            // Enrolled Student Link (null until converted)
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};