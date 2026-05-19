<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Intake;
use App\Models\Department;

echo "--- INTAKES ---\n";
foreach (Intake::all() as $intake) {
    echo "ID: {$intake->id} | Name: {$intake->name} | Active: " . ($intake->is_active ? 'YES' : 'NO') . " | Sort: {$intake->sort_order}\n";
}

echo "\n--- DEPARTMENTS ---\n";
foreach (Department::all() as $dept) {
    echo "ID: {$dept->id} | Name: {$dept->name} | Active: " . ($dept->is_active ? 'YES' : 'NO') . " | Sort: {$dept->sort_order}\n";
}
