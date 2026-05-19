<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

foreach (['hostel_applications', 'departments', 'intakes'] as $table) {
    echo "--- Table: $table ---\n";
    $columns = DB::select("SHOW COLUMNS FROM `$table`");
    foreach ($columns as $column) {
        echo "  {$column->Field} ({$column->Type})\n";
    }
}
