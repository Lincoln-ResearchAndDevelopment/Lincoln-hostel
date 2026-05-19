<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;

$s = Student::find(110);
if ($s) {
    echo "Student Name: {$s->full_name}\n";
    echo "Student Email: {$s->email}\n";
    echo "Parent Email: {$s->parent_email}\n";
    echo "Parent Phone: {$s->parent_phone}\n";
    if ($s->hostelApplication) {
        echo "App Parent Email: {$s->hostelApplication->parent_email}\n";
        echo "App Parent Phone: {$s->hostelApplication->parent_phone}\n";
    } else {
        echo "No Hostel Application associated.\n";
    }
} else {
    echo "Student not found!\n";
}
