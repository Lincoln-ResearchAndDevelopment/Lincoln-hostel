<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Department;
use App\Models\Intake;
use App\Models\HostelApplication;

echo "Current Counts:\n";
echo "Depts: " . Department::count() . "\n";
echo "Intakes: " . Intake::count() . "\n";
echo "Apps: " . HostelApplication::count() . "\n";

echo "\nAttempting to create a Dept...\n";
try {
    $d = Department::create([
        'name' => 'Debug Dept',
        'code' => 'DBG',
        'is_active' => true,
    ]);
    echo "Created Dept ID: " . $d->id . "\n";
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

echo "Final Counts:\n";
echo "Depts: " . Department::count() . "\n";
