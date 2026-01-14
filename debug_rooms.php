<?php

use App\Models\Room;
use App\Models\Hostel;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Total Hostels: " . Hostel::count() . "\n";
echo "Total Rooms: " . Room::count() . "\n\n";

echo "--- Room Details ---\n";
$rooms = Room::all();
foreach ($rooms as $room) {
    echo "ID: {$room->id} | Number: {$room->room_number} | Status: {$room->status} | Occupied: {$room->occupied} | Capacity: {$room->capacity} | Hostel ID: {$room->hostel_id}\n";
}

echo "\n--- Query Test ---\n";
$availableRooms = Room::with('hostel')
    ->where('status', 'available')
    ->whereRaw('occupied < capacity')
    ->get();

echo "Query returned " . $availableRooms->count() . " rooms.\n";
