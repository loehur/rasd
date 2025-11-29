<?php

/**
 * Manually migrate team_leader data to staff table
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->boot();

use Illuminate\Support\Facades\DB;

echo "Migrating Team Leader data to Staff table\n";
echo str_repeat('=', 70) . "\n\n";

// Get all team leaders
$teamLeaders = DB::table('team_leaders')->get();

echo "Found " . count($teamLeaders) . " team leaders to migrate\n\n";

$migrated = 0;
$updated = 0;
$skipped = 0;

foreach ($teamLeaders as $tl) {
    // Check if this employee_id already exists in staff table
    $existingStaff = DB::table('staff')->where('staff_id', $tl->employee_id)->first();

    if ($existingStaff) {
        // Check if already a TL
        if ($existingStaff->role === 'tl') {
            echo "  ✓ {$tl->employee_id} - {$tl->name} (already migrated)\n";
            $skipped++;
            continue;
        }

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

        echo "  ✓ {$tl->employee_id} - {$tl->name} (updated existing staff to TL)\n";
        $updated++;
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

        echo "  ✓ {$tl->employee_id} - {$tl->name} (inserted as new TL)\n";
        $migrated++;
    }
}

echo "\n";
echo str_repeat('=', 70) . "\n";
echo "Migration Summary:\n";
echo "  - New TL records inserted: $migrated\n";
echo "  - Existing staff updated to TL: $updated\n";
echo "  - Already migrated (skipped): $skipped\n";
echo "  - Total: " . ($migrated + $updated + $skipped) . "\n";
echo str_repeat('=', 70) . "\n";

// Verify
echo "\nVerification:\n";
$tlCount = DB::table('staff')->where('role', 'tl')->count();
echo "  - Team Leaders in staff table: $tlCount\n";
$staffCount = DB::table('staff')->where('role', 'staff')->count();
echo "  - Regular staff in staff table: $staffCount\n";
echo "\n";
