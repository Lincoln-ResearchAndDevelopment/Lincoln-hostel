<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

echo "--- STARTING LOGIN TEST ---\n";

$admissionNumber = '54678976';
$contactNumber = '456788765443';

echo "Testing credentials:\nAdmission: $admissionNumber\nContact: $contactNumber\n";

$student = Student::where('admission_number', $admissionNumber)
                  ->where('contact_number', $contactNumber)
                  ->first();

if (!$student) {
    echo "ERROR: Student not found in the database with these credentials.\n";
    // Check if they exist in hostel_applications
    $app = DB::table('hostel_applications')
        ->where('admission_number', $admissionNumber)
        ->first();
    if ($app) {
        echo "However, an application EXISTS for this admission number. Status: {$app->status}\n";
    } else {
        echo "No application exists for this admission number either.\n";
    }
} else {
    echo "SUCCESS: Student found in DB! ID: {$student->id}, Name: {$student->first_name} {$student->last_name}\n";
    
    // Test auth guard
    Auth::guard('student')->login($student);
    if (Auth::guard('student')->check()) {
        echo "SUCCESS: Auth guard 'student' successfully authenticated the user.\n";
    } else {
        echo "ERROR: Auth guard 'student' failed to authenticate the user.\n";
    }
}

echo "--- MIDDLEWARE CHECK ---\n";
// Let's check the redirect logic for RedirectIfNotStudent
$studentGuard = Auth::guard('student');
echo "Is student guard active? " . ($studentGuard->check() ? 'Yes' : 'No') . "\n";
