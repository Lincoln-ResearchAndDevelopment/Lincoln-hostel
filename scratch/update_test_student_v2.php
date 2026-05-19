<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$oldAdmission = 'LUC-NGA-002-ADM-8140374';
$oldContact = '+234 803 834 1496';

$newAdmission = '01026099999';
$newContact = '08038341496';

echo "Updating Student test data...\n";

// 1. Update Student
$student = \App\Models\Student::where('admission_number', $oldAdmission)->orWhere('admission_number', $newAdmission)->first();
if ($student) {
    $student->admission_number = $newAdmission;
    $student->contact_number = $newContact;
    $student->save();
    echo "- Updated students table\n";
    
    // 2. Update User (if exists and linked)
    if ($student->user_id) {
        $user = \App\Models\User::find($student->user_id);
        if ($user) {
            // Check if username was the old admission number
            if ($user->username === $oldAdmission) {
                $user->username = $newAdmission;
            }
            if ($user->phone === $oldContact) {
                $user->phone = $newContact;
            }
            $user->save();
            echo "- Updated users table\n";
        }
    }

    // 3. Update HostelApplication by student_id or application_number
    $app = \App\Models\HostelApplication::where('student_id', $oldAdmission)
            ->orWhere('application_number', $oldAdmission)
            ->orWhere('student_id', $newAdmission) // in case it was already partially updated
            ->first();
            
    if ($app) {
        if ($app->student_id === $oldAdmission) {
            $app->student_id = $newAdmission;
        }
        $app->phone_number = $newContact;
        $app->save();
        echo "- Updated hostel_applications table\n";
    }

    echo "\nUpdate complete! You can now log in with:\n";
    echo "Admission Number: {$newAdmission}\n";
    echo "Contact Number: {$newContact}\n";
} else {
    echo "- Student not found!\n";
}
