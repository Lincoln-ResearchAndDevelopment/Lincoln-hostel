<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;

$student = Student::find(110);
if ($student) {
    echo "SUCCESS: " . $student->admission_number . " | " . $student->contact_number . "\n";
} else {
    echo "ERROR: Test student not found.\n";
}
