<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;

echo "=== LATEST 5 REGISTERED STUDENTS ===\n";
$students = Student::with('room')->orderBy('id', 'desc')->limit(5)->get();
foreach ($students as $s) {
    echo "ID: {$s->id}, Name: {$s->full_name}, Email: {$s->email}, Room: " . ($s->room ? $s->room->room_number : 'None') . ", Created: {$s->created_at}\n";
}
