<?php
$log = file_get_contents('storage/logs/laravel.log');
// Find last occurrences of "local.ERROR"
$offset = 0;
$matches = [];
while (($pos = strpos($log, 'local.ERROR', $offset)) !== false) {
    $matches[] = $pos;
    $offset = $pos + 1;
}

// Print the last 3 errors
$lastMatches = array_slice($matches, -3);
foreach ($lastMatches as $pos) {
    echo "========================================\n";
    echo substr($log, $pos, 1000) . "\n";
}
