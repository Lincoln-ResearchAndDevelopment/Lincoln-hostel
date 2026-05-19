<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bed;
use App\Models\Room;

echo "Total Rooms: " . Room::count() . "\n";
echo "Total Beds: " . Bed::count() . "\n";
echo "Occupied Beds: " . Bed::where('is_occupied', true)->count() . "\n";
echo "Unoccupied Beds: " . Bed::where('is_occupied', false)->count() . "\n";

$beds = Bed::take(5)->get();
foreach ($beds as $bed) {
    echo "Bed ID: {$bed->id} | Room ID: {$bed->room_id} | Bed Number: {$bed->bed_number} | Occupied: " . ($bed->is_occupied ? 'Yes' : 'No') . "\n";
}
