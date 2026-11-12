<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Student Document Requests (Transcripts, Letters, Certifications)
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number', 30)->unique(); // e.g. DOC-2026-0001
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('request_type', [
                'Official Transcript', 
                'Enrollment Verification Letter', 
                'Medium of Instruction Certificate',
                'Letter of Recommendation'
            ]);
            $table->text('purpose');
            $table->enum('status', ['Pending', 'Processing', 'Issued', 'Rejected'])->default('Pending');
            $table->text('officer_remarks')->nullable();
            $table->foreignId('processed_by_user_id')->nullable()->constrained('users');
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });

        // 2. Multi-Office Graduation Clearance
        Schema::create('graduation_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            
            // 4-Point Institutional Clearance
            $table->enum('status_department', ['Pending', 'Cleared'])->default('Pending');
            $table->enum('status_library', ['Pending', 'Cleared'])->default('Pending');
            $table->enum('status_services', ['Pending', 'Cleared'])->default('Pending'); // Hostel/Sports
            $table->enum('status_finance', ['Pending', 'Cleared'])->default('Pending');  // Cost-Sharing
            
            // Overall Status
            $table->enum('overall_status', ['InClearance', 'ClearedForGraduation', 'Graduated'])->default('InClearance');
            $table->string('degree_awarded')->nullable();
            $table->timestamp('graduated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduation_clearances');
        Schema::dropIfExists('document_requests');
    }
};