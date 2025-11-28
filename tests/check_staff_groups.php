<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->boot();

echo "Checking available groups in staff table\n";
echo str_repeat('=', 70) . "\n";

$groups = \App\Models\Staff::select('group')
    ->distinct()
    ->whereNotNull('group')
    ->orderBy('group')
    ->pluck('group');

echo "Found " . count($groups) . " unique groups:\n\n";

foreach($groups as $group) {
    $count = \App\Models\Staff::where('group', $group)->count();
    echo sprintf("  - %-15s (%d staff)\n", $group, $count);
}

echo "\n";
