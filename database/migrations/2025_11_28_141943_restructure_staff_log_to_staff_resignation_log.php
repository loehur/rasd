<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename table from staff_log to staff_resignation_log
        Schema::rename('staff_log', 'staff_resignation_log');

        // Modify columns
        Schema::table('staff_resignation_log', function (Blueprint $table) {
            // Drop unnecessary columns
            $table->dropColumn(['action', 'field_name', 'old_value', 'new_value']);

            // Rename changed_by to submitted_by
            $table->renameColumn('changed_by', 'submitted_by');

            // Add reason column
            $table->text('reason')->nullable()->after('report_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse column changes first
        Schema::table('staff_resignation_log', function (Blueprint $table) {
            // Remove reason column
            $table->dropColumn('reason');

            // Rename submitted_by back to changed_by
            $table->renameColumn('submitted_by', 'changed_by');

            // Add back the dropped columns
            $table->string('action')->after('staff_id');
            $table->string('field_name')->nullable()->after('action');
            $table->text('old_value')->nullable()->after('field_name');
            $table->text('new_value')->nullable()->after('old_value');
        });

        // Rename table back to staff_log
        Schema::rename('staff_resignation_log', 'staff_log');
    }
};
