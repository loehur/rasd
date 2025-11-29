<?php

/**
 * List all admin users
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load Lumen app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->boot();

echo "Listing all users:\n";
echo str_repeat('=', 70) . "\n";

$users = \App\Models\User::all();

if ($users->isEmpty()) {
    echo "No users found in database.\n";
} else {
    foreach ($users as $user) {
        echo "ID: {$user->id}\n";
        echo "  Name: {$user->name}\n";
        echo "  Phone: {$user->phone_number}\n";
        echo "  Role: {$user->role}\n";
        echo "  Password Hash: " . substr($user->password, 0, 60) . "...\n";
        echo str_repeat('-', 70) . "\n";
    }
}

echo "\n";
