<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DATABASE CONFIGURATION ===\n";
echo "Default Connection: " . config('database.default') . "\n";
$connection = DB::connection();
echo "Active Database Name: " . $connection->getDatabaseName() . "\n";
echo "Host: " . config('database.connections.mysql.host') . "\n";
echo "Database: " . config('database.connections.mysql.database') . "\n";
echo "Username: " . config('database.connections.mysql.username') . "\n";

echo "\n=== HOSTEL APPLICATIONS IN THIS DATABASE ===\n";
$apps = DB::table('hostel_applications')->where('email', 'chimezietchris@gmail.com')->get();
echo "Found " . $apps->count() . " application(s):\n";
foreach ($apps as $a) {
    echo "  - ID: {$a->id}, App Num: {$a->application_number}, Name: {$a->full_name}, Status: {$a->status}, Student ID: {$a->student_id}\n";
}

$allCount = DB::table('hostel_applications')->count();
echo "Total Applications in DB: $allCount\n";
