<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'academic_advisor_user_id')) {
                $table->foreignId('academic_advisor_user_id')
                      ->nullable()
                      ->after('program_id')
                      ->constrained('users')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'academic_advisor_user_id')) {
                $table->dropForeign(['academic_advisor_user_id']);
                $table->dropColumn('academic_advisor_user_id');
            }
        });
    }
};