<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Recent 10 Hostel Applications ---\n";
$applications = App\Models\HostelApplication::orderBy('id', 'desc')->take(10)->get();
foreach ($applications as $appRecord) {
    echo "ID: {$appRecord->id}, App Num: {$appRecord->application_number}, Name: {$appRecord->first_name} {$appRecord->last_name}, Email: {$appRecord->email}, Status: {$appRecord->status}, Room: {$appRecord->room_id}\n";
}

echo "\n--- Recent 10 Students ---\n";
$students = App\Models\Student::orderBy('id', 'desc')->take(10)->get();
foreach ($students as $student) {
    echo "ID: {$student->id}, Name: {$student->first_name} {$student->last_name}, Adm Num: {$student->admission_number}, Email: {$student->email}, Status: {$student->hostel_fee_status}, Room: {$student->room_id}\n";
}
