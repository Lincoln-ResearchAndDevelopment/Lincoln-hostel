<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Room;
use App\Models\Bed;
use App\Models\Student;

echo "Fixing database beds...\n";

$rooms = Room::all();
foreach ($rooms as $room) {
    $bedCount = Bed::where('room_id', $room->id)->count();
    if ($bedCount == 0) {
        echo "Room ID: {$room->id} (Room Number: {$room->room_number}, Capacity: {$room->capacity}, Occupied: {$room->occupied}) has 0 beds. Creating beds...\n";
        
        // Find students in this room
        $students = Student::where('room_id', $room->id)->get();
        
        for ($b = 1; $b <= $room->capacity; $b++) {
            $studentId = null;
            $isOccupied = false;
            
            if (isset($students[$b - 1])) {
                $studentId = $students[$b - 1]->id;
                $isOccupied = true;
            }
            
            $bed = Bed::create([
                'room_id' => $room->id,
                'bed_number' => "Bed $b",
                'is_occupied' => $isOccupied,
                'student_id' => $studentId,
            ]);
            
            if ($studentId) {
                // Associate student with this bed
                $students[$b - 1]->update(['bed_id' => $bed->id]);
            }
        }
    }
}

echo "Database beds fixed successfully!\n";
echo "Total Beds now: " . Bed::count() . "\n";
echo "Unoccupied Beds now: " . Bed::where('is_occupied', false)->count() . "\n";
