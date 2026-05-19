<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HostelApplication;
use App\Models\Student;
use App\Models\User;

echo "--- APPLICATIONS ---\n";
$apps = HostelApplication::where('email', 'chimezietchris@gmail.com')->get();
foreach ($apps as $a) {
    echo "ID: {$a->id} | Name: {$a->full_name} | Number: {$a->application_number} | Status: {$a->status} | Student ID: {$a->student_id}\n";
}

echo "\n--- STUDENTS ---\n";
$students = Student::where('email', 'chimezietchris@gmail.com')->get();
foreach ($students as $s) {
    echo "ID: {$s->id} | Name: {$s->full_name} | Adm Num: {$s->admission_number} | Room ID: {$s->room_id} | Bed ID: {$s->bed_id}\n";
}

echo "\n--- USERS ---\n";
$users = User::where('email', 'chimezietchris@gmail.com')->get();
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Role: {$u->role}\n";
}
