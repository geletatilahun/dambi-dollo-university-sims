<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advising_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('advising_notes', 'session_date')) {
                $table->date('session_date')->nullable()->after('advisor_user_id');
            }
            if (!Schema::hasColumn('advising_notes', 'observations')) {
                $table->text('observations')->nullable()->after('intervention_type');
            }
            if (!Schema::hasColumn('advising_notes', 'recommendations')) {
                $table->text('recommendations')->nullable()->after('observations');
            }
            if (!Schema::hasColumn('advising_notes', 'is_confidential')) {
                $table->boolean('is_confidential')->default(false)->after('recommendations');
            }
        });
    }

    public function down(): void
    {
        Schema::table('advising_notes', function (Blueprint $table) {
            $columns = ['is_confidential', 'recommendations', 'observations', 'session_date'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('advising_notes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};