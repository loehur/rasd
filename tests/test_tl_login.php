<?php

/**
 * Test Team Leader Login
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->boot();

use App\Models\TeamLeader;

echo "Testing Team Leader Login\n";
echo str_repeat('=', 70) . "\n\n";

// Test 1: Check if we can find a team leader
echo "1. Finding Team Leader by staff_id:\n";
echo str_repeat('-', 70) . "\n";
$employeeId = 'IDNA200046'; // From the error message
$tl = TeamLeader::where('staff_id', $employeeId)->first();

if ($tl) {
    echo "  ✓ Found TL: {$tl->name}\n";
    echo "  ✓ Staff ID: {$tl->staff_id}\n";
    echo "  ✓ Role: {$tl->role}\n";
    echo "  ✓ Position: {$tl->position}\n";
    echo "  ✓ Team: {$tl->team}\n";
    echo "  ✓ Has password: " . ($tl->password ? "Yes" : "No") . "\n";

    // Test password
    echo "\n2. Testing Password:\n";
    echo str_repeat('-', 70) . "\n";
    $defaultWorks = password_verify('tl1230', $tl->password);
    echo "  - Default password 'tl1230': " . ($defaultWorks ? "✓ Works" : "✗ Doesn't work") . "\n";

    echo "\n3. Testing employee_id accessor:\n";
    echo str_repeat('-', 70) . "\n";
    echo "  - employee_id (via accessor): {$tl->employee_id}\n";
    echo "  - staff_id (direct): {$tl->staff_id}\n";
    echo "  - Match: " . ($tl->employee_id === $tl->staff_id ? "✓ Yes" : "✗ No") . "\n";
} else {
    echo "  ✗ Team Leader not found!\n";
    echo "\n  Listing all Team Leaders:\n";
    $allTLs = TeamLeader::all();
    foreach ($allTLs as $t) {
        echo "    - {$t->staff_id} | {$t->name}\n";
    }
}

echo "\n";
echo str_repeat('=', 70) . "\n";
echo "Test Complete!\n";
