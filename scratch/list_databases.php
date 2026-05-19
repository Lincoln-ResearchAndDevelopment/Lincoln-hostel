<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $databases = DB::select('SHOW DATABASES');
    echo "=== ALL DATABASES ON MYSQL ===\n";
    foreach ($databases as $db) {
        $dbName = $db->Database;
        // Count applications if the table exists
        $appCount = 'N/A';
        try {
            $count = DB::select("SELECT COUNT(*) as cnt FROM `{$dbName}`.hostel_applications");
            $appCount = $count[0]->cnt;
        } catch (\Exception $e) {
            // Table doesn't exist in this DB
        }
        echo " - Database: {$dbName} (Hostel Applications count: {$appCount})\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
