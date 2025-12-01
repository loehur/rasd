<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new change types to the enum
        DB::statement("ALTER TABLE staff_logs MODIFY COLUMN change_type ENUM(
            'division_transfer',
            'promotion',
            'rank_change',
            'warning_letter',
            'team_leader_transfer',
            'status_change'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE staff_logs MODIFY COLUMN change_type ENUM(
            'division_transfer',
            'promotion',
            'rank_change',
            'warning_letter'
        )");
    }
};
