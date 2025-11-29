<?php

/**
 * Team Leader Password Reset Tool
 *
 * Usage: php tests/reset_team_leader_password.php [employee_id] [new_password]
 * Example: php tests/reset_team_leader_password.php IDNA102557 tl3313
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load Lumen app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->boot();

// Get parameters from command line
$employeeId = $argv[1] ?? null;
$newPassword = $argv[2] ?? 'tl1230';

if (!$employeeId) {
    echo "Usage: php tests/reset_team_leader_password.php [employee_id] [new_password]\n";
    echo "Example: php tests/reset_team_leader_password.php IDNA102557 tl3313\n";
    echo "\nDefault password is 'tl1230' if not specified.\n";
    exit(1);
}

echo "Resetting password for team leader\n";
echo str_repeat('=', 70) . "\n";
echo "Employee ID: $employeeId\n";
echo "New Password: $newPassword\n";
echo str_repeat('=', 70) . "\n\n";

// Query database - now using staff_id since TeamLeader uses staff table
$teamLeader = \App\Models\TeamLeader::where('staff_id', $employeeId)->first();

if (!$teamLeader) {
    echo "ERROR: Team leader not found!\n";
    exit(1);
}

echo "Found: {$teamLeader->name}\n";
echo "Old password hash: " . substr($teamLeader->password, 0, 40) . "...\n\n";

// Reset password - pass plain text, model will hash it via mutator
$teamLeader->password = $newPassword;
$teamLeader->save();

// Reload from database to verify
$teamLeader = $teamLeader->fresh();

echo "✓ Password reset successful!\n";
echo "New password hash: " . substr($teamLeader->password, 0, 40) . "...\n\n";

// Verify
echo "Verification:\n";
$verified = password_verify($newPassword, $teamLeader->password);
$icon = $verified ? '✓' : '✗';
$status = $verified ? 'MATCH' : 'NO MATCH';
echo "  - Verify '$newPassword': $icon $status\n\n";

if ($verified) {
    echo "SUCCESS! You can now login with:\n";
    echo "  Employee ID: $employeeId\n";
    echo "  Password: $newPassword\n";
} else {
    echo "WARNING: Password verification failed! There might be an issue.\n";
}

echo "\n";
