<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Audited Grade Change Requests
        Schema::create('grade_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_course_id')->constrained()->cascadeOnDelete();
            $table->decimal('previous_score', 5, 2);
            $table->string('previous_grade', 5);
            $table->decimal('proposed_score', 5, 2);
            $table->string('proposed_grade', 5);
            $table->text('reason'); // e.g. "Calculation error on final exam question 4"
            $table->enum('status', ['Pending', 'DeptEndorsed', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('requested_by_user_id')->constrained('users');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // 2. University Announcements & Bulletins
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('target_audience', ['All', 'Students', 'Instructors'])->default('All');
            $table->foreignId('posted_by_user_id')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('grade_change_requests');
    }
};