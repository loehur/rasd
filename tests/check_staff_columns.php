<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->boot();

echo "Checking staff table columns:\n";
echo str_repeat('=', 70) . "\n";

$columns = \Illuminate\Support\Facades\DB::select('DESCRIBE staff');

foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

echo "\nChecking if 'role' column exists: ";
$hasRole = collect($columns)->contains('Field', 'role');
echo $hasRole ? "YES\n" : "NO\n";

echo "\nChecking if 'password' column exists: ";
$hasPassword = collect($columns)->contains('Field', 'password');
echo $hasPassword ? "YES\n" : "NO\n";
