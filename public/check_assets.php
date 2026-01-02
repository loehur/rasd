<?php
echo "<h1>Asset Diagnostic</h1>";
$path = __DIR__ . '/assets';
echo "Checking path: " . $path . "<br>";

if (!is_dir($path)) {
    die("❌ Folder 'assets' DOES NOT EXIST.");
}

echo "✅ Folder 'assets' exists.<br>";
echo "Permissions: " . substr(sprintf('%o', fileperms($path)), -4) . "<br>";
echo "Owner: " . fileowner($path) . "<br><br>";

echo "<h3>Files in directory:</h3>";
$files = scandir($path);
echo "<ul>";
foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    $fullPath = $path . '/' . $file;
    $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
    $readable = is_readable($fullPath) ? 'READABLE' : 'NOT READABLE';
    echo "<li><strong>$file</strong> - $perms - $readable</li>";
}
echo "</ul>";

echo "<h3>Test Read Specific File:</h3>";
$target = 'teamLeaderLogin-DhcjuoVo.js'; // Validate with one of the missing files
$targetPath = $path . '/' . $target;
if (file_exists($targetPath)) {
    echo "✅ Target file '$target' found.<br>";
    echo "Size: " . filesize($targetPath) . " bytes<br>";
} else {
    echo "❌ Target file '$target' NOT FOUND.<br>";
    // Check for case sensitivity issues
    $found = false;
    foreach ($files as $f) {
        if (strtolower($f) == strtolower($target)) {
            echo "⚠️ But found '$f' (Case mismatch!)<br>";
            $found = true;
        }
    }
    if (!$found) echo "File is completely missing.<br>";
}
