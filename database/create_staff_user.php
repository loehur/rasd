<?php

/**
 * Script to create a test staff user
 * Run this file from command line: php database/create_staff_user.php
 */

// Define the password
$password = 'Staff123!';

// Generate bcrypt hash
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

echo "===== Staff User Creation Script =====\n\n";
echo "Password: {$password}\n";
echo "Bcrypt Hash: {$hashedPassword}\n\n";

// Output SQL query
$sql = "INSERT INTO `users` (`name`, `phone_number`, `password`, `role`, `created_at`, `updated_at`) 
VALUES (
    'Staff Test User',
    '08123456789',
    '{$hashedPassword}',
    'staff',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    `name` = 'Staff Test User',
    `password` = '{$hashedPassword}',
    `role` = 'staff',
    `updated_at` = NOW();";

echo "SQL Query to run:\n";
echo "==================\n";
echo $sql . "\n\n";

// If you want to auto-execute this (requires database connection)
// Uncomment the following code:

/*
require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;

try {
    DB::insert($sql);
    echo "✓ Staff user created successfully!\n";
    echo "  Phone Number: 08123456789\n";
    echo "  Password: {$password}\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
*/

echo "You can copy the SQL above and run it in your database client,\n";
echo "or uncomment the auto-execution code in this file.\n\n";
echo "Login Credentials:\n";
echo "  Phone Number: 08123456789\n";
echo "  Password: {$password}\n";
