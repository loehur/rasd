<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration merges team_leaders table into staff table by:
     * 1. Adding necessary fields to staff table (role, password, etc.)
     * 2. Migrating all team_leader data to staff table with role='tl'
     * 3. Keeping team_leaders table for now (can be dropped later)
     */
    public function up(): void
    {
        // Step 1: Add new fields to staff table (if they don't exist)
        Schema::table('staff', function (Blueprint $table) {
            if (!Schema::hasColumn('staff', 'role')) {
                $table->enum('role', ['staff', 'tl'])->default('staff')->after('staff_id');
            }
            if (!Schema::hasColumn('staff', 'password')) {
                $table->string('password')->nullable()->after('role');
            }
            if (!Schema::hasColumn('staff', 'team')) {
                $table->string('team')->nullable()->after('group');
            }
            if (!Schema::hasColumn('staff', 'team_quantity')) {
                $table->integer('team_quantity')->nullable()->after('team');
            }
            if (!Schema::hasColumn('staff', 'first_day_tl')) {
                $table->date('first_day_tl')->nullable()->after('hire_date');
            }
            if (!Schema::hasColumn('staff', 'former_tl')) {
                $table->string('former_tl')->nullable()->after('team_leader_id');
            }
        });

        // Step 2: Migrate data from team_leaders to staff
        $teamLeaders = DB::table('team_leaders')->get();

        foreach ($teamLeaders as $tl) {
            // Check if this employee_id already exists in staff table
            $existingStaff = DB::table('staff')->where('staff_id', $tl->employee_id)->first();

            if ($existingStaff) {
                // Update existing record to make it a TL
                DB::table('staff')
                    ->where('staff_id', $tl->employee_id)
                    ->update([
                        'role' => 'tl',
                        'password' => $tl->password,
                        'team' => $tl->team,
                        'team_quantity' => $tl->team_quantity,
                        'first_day_tl' => $tl->first_day_tl,
                        'former_tl' => $tl->former_tl,
                        'position' => $tl->position ?? $existingStaff->position,
                        'work_location' => $tl->work_location ?? $existingStaff->work_location,
                        'department' => $tl->department ?? $existingStaff->department,
                        'hire_date' => $tl->hire_date ?? $existingStaff->hire_date,
                        'rank' => $tl->rank ?? $existingStaff->rank,
                        'area' => $tl->area ?? $existingStaff->area,
                        'warning_letter' => $tl->warning_letter ?? $existingStaff->warning_letter,
                        'ojk_case' => $tl->ojk_case ?? $existingStaff->ojk_case,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            } else {
                // Insert new record as TL
                DB::table('staff')->insert([
                    'staff_id' => $tl->employee_id,
                    'role' => 'tl',
                    'name' => $tl->name,
                    'password' => $tl->password,
                    'phone_number' => $tl->employee_id, // Use employee_id as phone if not available
                    'position' => $tl->position ?? 'DC TL',
                    'department' => $tl->department,
                    'work_location' => $tl->work_location,
                    'hire_date' => $tl->hire_date,
                    'rank' => $tl->rank,
                    'area' => $tl->area,
                    'team' => $tl->team,
                    'team_quantity' => $tl->team_quantity,
                    'first_day_tl' => $tl->first_day_tl,
                    'former_tl' => $tl->former_tl,
                    'warning_letter' => $tl->warning_letter,
                    'ojk_case' => $tl->ojk_case ?? 0,
                    'staff_status' => 'active',
                    'created_at' => $tl->created_at ?? date('Y-m-d H:i:s'),
                    'updated_at' => $tl->updated_at ?? date('Y-m-d H:i:s'),
                ]);
            }
        }

        echo "Migrated " . count($teamLeaders) . " team leaders to staff table.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove TL records from staff table
        DB::table('staff')->where('role', 'tl')->delete();

        // Remove added columns
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['role', 'password', 'team', 'team_quantity', 'first_day_tl', 'former_tl']);
        });
    }
};
