<?php

// Load Laravel Bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Hostel;
use App\Models\Room;
use App\Models\Bed;
use App\Models\Student;

echo "--- HOSTELS ---\n";
$hostels = Hostel::all();
foreach ($hostels as $hostel) {
    echo "ID: {$hostel->id} | Name: {$hostel->name} | Gender: {$hostel->gender_type} | Active: {$hostel->is_active}\n";
}

echo "\n--- AVAILABLE ROOMS ---\n";
$rooms = Room::whereRaw('occupied < capacity')->get();
foreach ($rooms as $room) {
    echo "ID: {$room->id} | Hostel ID: {$room->hostel_id} | Room Number: {$room->room_number} | Occupied: {$room->occupied} / {$room->capacity} | Status: {$room->status}\n";
}

echo "\n--- AVAILABLE BEDS ---\n";
$beds = Bed::where('is_occupied', false)->take(10)->get();
foreach ($beds as $bed) {
    echo "ID: {$bed->id} | Room ID: {$bed->room_id} | Bed Number: {$bed->bed_number} | Occupied: {$bed->is_occupied}\n";
}
