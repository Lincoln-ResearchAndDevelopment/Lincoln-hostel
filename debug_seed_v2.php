<?php

use Illuminate\Support\Facades\DB;
use App\Models\Hostel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Starting Hostel Seed Test...\n";
    
    // Clear hostels
    Hostel::truncate();
    echo "Truncated hostels.\n";

    $hostelData = [
        'name' => 'Boys Hostel',
        'code' => 'BHS',
        'type' => 'male',
        'address' => 'Test Address',
        'description' => 'Test Description',
        'status' => 'active',
    ];

    $hostel = Hostel::create($hostelData);
    echo "Created Hostel: " . $hostel->name . " (ID: " . $hostel->id . ")\n";

    $fetched = Hostel::where('code', 'BHS')->first();
    if ($fetched) {
        echo "Successfully fetched BHS.\n";
    } else {
        echo "Failed to fetch BHS.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
