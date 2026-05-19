<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$student = \App\Models\Student::where('admission_number', 'LUC-NGA-002-ADM-8140374')
    ->orWhere('email', 'chimezietchris@gmail.com')
    ->first();

if ($student) {
    echo json_encode($student->toArray(), JSON_PRETTY_PRINT);
} else {
    echo 'Student not found in students table' . PHP_EOL;
}
