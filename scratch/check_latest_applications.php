<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$latest = DB::table('hostel_applications')
    ->orderBy('id', 'desc')
    ->limit(15)
    ->get();

echo "=== LATEST 15 APPLICATIONS IN DATABASE ===\n";
foreach ($latest as $a) {
    echo "ID: {$a->id} | App Num: {$a->application_number} | Name: {$a->full_name} | Email: {$a->email} | Status: {$a->status} | Created: {$a->created_at}\n";
}
