<?php

use App\Models\User;
use App\Models\Hostel;
use App\Models\Room;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $userCount = User::count();
    $hostelCount = Hostel::count();
    $roomCount = Room::count();

    echo "Users: $userCount\n";
    echo "Hostels: $hostelCount\n";
    echo "Rooms: $roomCount\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
