<?php

/**
 * Team Leader Login Test Tool
 *
 * Usage: php tests/test_login.php [employee_id] [password]
 * Example: php tests/test_login.php IDNA102557 tl3313
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load Lumen app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->boot();

// Get parameters
$employeeId = $argv[1] ?? null;
$password = $argv[2] ?? null;

if (!$employeeId || !$password) {
    echo "Usage: php tests/test_login.php [employee_id] [password]\n";
    echo "Example: php tests/test_login.php IDNA102557 tl3313\n";
    exit(1);
}

echo "Testing Team Leader Login\n";
echo str_repeat('=', 70) . "\n";
echo "Employee ID: $employeeId\n";
echo "Password: " . str_repeat('*', strlen($password)) . " ($password)\n";
echo str_repeat('=', 70) . "\n\n";

// Find team leader
$teamLeader = \App\Models\TeamLeader::where('employee_id', $employeeId)->first();

if (!$teamLeader) {
    echo "✗ FAIL: Team leader not found!\n";
    exit(1);
}

echo "Step 1: Team leader found\n";
echo "  Name: {$teamLeader->name}\n";
echo "  Position: " . ($teamLeader->position ?? '-') . "\n";
echo "  ✓ PASS\n\n";

// Verify password
echo "Step 2: Verify password\n";
$verified = password_verify($password, $teamLeader->password);

if (!$verified) {
    echo "  ✗ FAIL: Password does not match!\n";
    echo "  Database hash: " . substr($teamLeader->password, 0, 40) . "...\n";
    exit(1);
}

echo "  ✓ PASS: Password verified\n\n";

// Check if default password
echo "Step 3: Check if using default password\n";
$isDefaultPassword = password_verify('tl1230', $teamLeader->password);
echo "  Is default password (tl1230): " . ($isDefaultPassword ? 'YES' : 'NO') . "\n";
echo "  ✓ PASS\n\n";

// Generate token
echo "Step 4: Generate authentication token\n";
$token = base64_encode($teamLeader->employee_id . ':' . time());
echo "  Token: " . substr($token, 0, 30) . "...\n";
echo "  ✓ PASS\n\n";

echo str_repeat('=', 70) . "\n";
echo "✓ LOGIN SUCCESS!\n";
echo str_repeat('=', 70) . "\n";
echo "\nLogin Response:\n";
echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'token' => $token,
    'is_default_password' => $isDefaultPassword,
    'user' => [
        'employee_id' => $teamLeader->employee_id,
        'name' => $teamLeader->name,
        'position' => $teamLeader->position,
        'team' => $teamLeader->team,
        'department' => $teamLeader->department,
    ]
], JSON_PRETTY_PRINT) . "\n\n";

if ($isDefaultPassword) {
    echo "NOTE: User is using default password and will be prompted to change it.\n";
}
