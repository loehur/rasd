<?php

/**
 * Team Leader Password Check Tool
 *
 * Usage: php tests/check_team_leader.php [employee_id]
 * Example: php tests/check_team_leader.php IDNA102557
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load Lumen app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->boot();

// Get employee ID from command line argument
$employeeId = $argv[1] ?? null;

if (!$employeeId) {
    echo "Usage: php tests/check_team_leader.php [employee_id]\n";
    echo "Example: php tests/check_team_leader.php IDNA102557\n\n";

    // Show all team leaders
    echo "Available team leaders:\n";
    echo str_repeat('=', 70) . "\n";
    $all = \App\Models\TeamLeader::select('employee_id', 'name', 'position')
        ->orderBy('name')
        ->get();

    foreach ($all as $tl) {
        echo sprintf("%-15s %-30s %s\n", $tl->employee_id, $tl->name, $tl->position ?? '-');
    }
    exit(0);
}

echo "Checking team leader: $employeeId\n";
echo str_repeat('=', 70) . "\n";

// Query database
$teamLeader = \App\Models\TeamLeader::where('employee_id', $employeeId)->first();

if (!$teamLeader) {
    echo "ERROR: Team leader not found!\n";
    echo "Employee ID searched: $employeeId\n\n";
    exit(1);
}

echo "Found: {$teamLeader->name}\n";
echo "Employee ID: {$teamLeader->employee_id}\n";
echo "Position: " . ($teamLeader->position ?? '-') . "\n";
echo "Department: " . ($teamLeader->department ?? '-') . "\n";
echo "Team: " . ($teamLeader->team ?? '-') . "\n";
echo "\nPassword Information:\n";
echo "  Hash: " . substr($teamLeader->password, 0, 40) . "...\n";
echo "  Length: " . strlen($teamLeader->password) . " chars\n";
echo "  Algorithm: " . (password_get_info($teamLeader->password)['algoName'] ?? 'unknown') . "\n";

echo "\nPassword verification:\n";
$testPasswords = ['tl1230', 'Admin123!', 'tl3313'];
foreach ($testPasswords as $pwd) {
    $match = password_verify($pwd, $teamLeader->password);
    $icon = $match ? '✓' : '✗';
    $status = $match ? 'MATCH' : 'NO MATCH';
    echo sprintf("  - %-15s %s %s\n", "'$pwd':", $icon, $status);
}

echo "\n";
