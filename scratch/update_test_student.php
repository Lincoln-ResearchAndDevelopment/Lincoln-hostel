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
$student = \App\Models\Student::where('admission_number', $oldAdmission)->first();
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
} else {
    echo "- Student not found!\n";
}

// 3. Update HostelApplication
$app = \App\Models\HostelApplication::where('admission_number', $oldAdmission)->first();
if ($app) {
    $app->admission_number = $newAdmission;
    $app->phone = $newContact;
    $app->save();
    echo "- Updated hostel_applications table\n";
}

// 4. Update Payments (if they store admission number directly, though they usually link by student_id)
// We will check just in case
$payments = \App\Models\Payment::where('student_id', $student->id ?? 0)->get();
echo "- Found {$payments->count()} payments (no update needed as they link by ID)\n";

echo "\nUpdate complete! You can now log in with:\n";
echo "Admission Number: {$newAdmission}\n";
echo "Contact Number: {$newContact}\n";
