<?php

/**
 * Admin Password Reset Tool
 *
 * Usage: php tests/reset_admin_password.php [phone_number] [new_password]
 * Example: php tests/reset_admin_password.php 081234567890 admin123
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load Lumen app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->boot();

// Get parameters from command line
$phoneNumber = $argv[1] ?? null;
$newPassword = $argv[2] ?? 'admin123';

if (!$phoneNumber) {
    echo "Usage: php tests/reset_admin_password.php [phone_number] [new_password]\n";
    echo "Example: php tests/reset_admin_password.php 081234567890 admin123\n";
    echo "\nDefault password is 'admin123' if not specified.\n";
    exit(1);
}

echo "Resetting password for admin\n";
echo str_repeat('=', 70) . "\n";
echo "Phone Number: $phoneNumber\n";
echo "New Password: $newPassword\n";
echo str_repeat('=', 70) . "\n\n";

// Query database
$user = \App\Models\User::where('phone_number', $phoneNumber)->first();

if (!$user) {
    echo "ERROR: User not found!\n";
    echo "\nListing all admin users:\n";
    echo str_repeat('-', 70) . "\n";

    $users = \App\Models\User::all();
    foreach ($users as $u) {
        echo "ID: {$u->id} | Name: {$u->name} | Phone: {$u->phone_number} | Role: {$u->role}\n";
    }
    echo str_repeat('-', 70) . "\n";
    exit(1);
}

echo "Found: {$user->name} ({$user->role})\n";
echo "Old password hash: " . substr($user->password, 0, 40) . "...\n\n";

// Reset password - pass plain text, model will hash it via mutator
$user->password = $newPassword;
$user->save();

// Reload from database to verify
$user = $user->fresh();

echo "✓ Password reset successful!\n";
echo "New password hash: " . substr($user->password, 0, 40) . "...\n\n";

// Verify
echo "Verification:\n";
$verified = password_verify($newPassword, $user->password);
$icon = $verified ? '✓' : '✗';
$status = $verified ? 'MATCH' : 'NO MATCH';
echo "  - Verify '$newPassword': $icon $status\n\n";

if ($verified) {
    echo "SUCCESS! You can now login with:\n";
    echo "  Phone Number: $phoneNumber\n";
    echo "  Password: $newPassword\n";
} else {
    echo "WARNING: Password verification failed! There might be an issue.\n";
}

echo "\n";
