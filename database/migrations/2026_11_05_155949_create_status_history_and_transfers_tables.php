<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Audit Trail for Student Status Changes
        Schema::create('student_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('previous_status', 30);
            $table->string('new_status', 30);
            $table->text('reason'); // e.g. "Medical Leave of Absence approved by University Senate"
            $table->foreignId('changed_by_user_id')->constrained('users');
            $table->timestamps();
        });

        // 2. Program & Department Transfer Requests
        Schema::create('program_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_program_id')->constrained('programs')->restrictOnDelete();
            $table->foreignId('to_program_id')->constrained('programs')->restrictOnDelete();
            $table->text('reason');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('reviewer_remarks')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_transfers');
        Schema::dropIfExists('student_status_histories');
    }
};