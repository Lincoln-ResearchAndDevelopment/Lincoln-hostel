<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Set connection to SQLite temporarily
config(['database.default' => 'sqlite']);
config(['database.connections.sqlite.database' => __DIR__ . '/../database/database.sqlite']);

try {
    echo "=== HOSTEL APPLICATIONS IN SQLITE ===\n";
    $apps = DB::connection('sqlite')
        ->table('hostel_applications')
        ->whereIn('id', [147, 148])
        ->get();
        
    foreach ($apps as $a) {
        echo "ID: {$a->id} | App Num: {$a->application_number} | Name: {$a->full_name} | Email: {$a->email} | Status: {$a->status}\n";
    }
    
    $total = DB::connection('sqlite')->table('hostel_applications')->count();
    echo "Total Applications in SQLite: $total\n";
} catch (\Exception $e) {
    echo "SQLite Error: " . $e->getMessage() . "\n";
}
