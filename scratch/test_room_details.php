<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate what the roomDetails() controller does
$student = \App\Models\Student::where('admission_number', 'LUC-NGA-002-ADM-8140374')->first();

if (!$student) {
    echo "Student not found\n";
    exit;
}

echo "Student: {$student->full_name}\n";
echo "Room ID: {$student->room_id}\n";
echo "Bed ID: {$student->bed_id}\n";

// Load relationships like the controller does
$student->load(['room.hostel', 'room.students', 'bed']);

if (!$student->room) {
    echo "No room assigned\n";
    exit;
}

$room = $student->room;
$hostel = $room->hostel;

echo "Room: {$room->room_number}\n";
echo "Hostel: {$hostel->name}\n";
echo "Room Type: {$room->room_type_display}\n";
echo "Hostel Type Badge: {$hostel->type_badge}\n";
echo "Hostel Status Badge: {$hostel->status_badge}\n";
echo "Bed Number (via accessor): " . ($student->bed_number ?? 'N/A') . "\n";
echo "Formatted Check-in: {$student->formatted_check_in_date}\n";
echo "Formatted Check-out: {$student->formatted_check_out_date}\n";

// Test roommates query (the one that was crashing)
try {
    $roommates = $student->room->students()->select([
        'students.id', 
        'students.full_name', 
        'students.department', 
        'students.bed_id', 
        'students.check_in_date', 
        'students.status', 
        'students.gender'
    ])->where('students.id', '!=', $student->id)->get();
    
    echo "Roommates count: {$roommates->count()}\n";
    foreach ($roommates as $rm) {
        echo "  - {$rm->full_name} (Bed ID: {$rm->bed_id})\n";
    }
    echo "\nALL CHECKS PASSED - Room details page should load correctly!\n";
} catch (\Exception $e) {
    echo "ERROR in roommates query: {$e->getMessage()}\n";
}
