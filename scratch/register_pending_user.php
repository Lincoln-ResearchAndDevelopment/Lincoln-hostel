<?php

/**
 * Register Pending Application for Manual Admin Approval Test
 * 
 * This script creates a pending application for "Praize Tchris" with email "chimezietchris@gmail.com".
 * It triggers the first "Application Received" email, then halts so that the admin can
 * manually approve it from the dashboard.
 */

// 1. Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HostelApplication;
use App\Models\Student;
use App\Models\User;
use App\Models\Room;
use App\Models\Bed;
use App\Models\Intake;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

echo "=========================================================\n";
echo "🚀 REGISTERING PENDING TEST APPLICATION FOR MANUAL APPROVAL\n";
echo "=========================================================\n\n";

try {
    // Cleanup any existing student/user/application with target email to avoid duplicate key issues
    echo "🧹 Cleaning up existing test records for chimezietchris@gmail.com...\n";
    
    $student = Student::where('email', 'chimezietchris@gmail.com')
                      ->orWhere('full_name', 'Praize Tchris')
                      ->first();

    if ($student) {
        DB::transaction(function () use ($student) {
            $roomId = $student->room_id;
            $bedId = $student->bed_id;
            $userId = $student->user_id;

            if ($roomId) {
                Room::where('id', $roomId)->where('occupied', '>', 0)->update([
                    'occupied' => DB::raw('occupied - 1'),
                    'status' => DB::raw('CASE WHEN occupied - 1 < capacity AND status = "full" THEN "available" ELSE status END'),
                ]);
            }

            if ($bedId) {
                Bed::where('id', $bedId)->update([
                    'is_occupied' => false,
                    'student_id' => null,
                ]);
            }

            $student->delete();
            if ($userId) {
                User::where('id', $userId)->delete();
            }
        });
        echo "- Existing student record and associated user/room details cleared.\n";
    }

    HostelApplication::where('email', 'chimezietchris@gmail.com')
                     ->orWhere('full_name', 'Praize Tchris')
                     ->delete();
    echo "- Existing application records cleared.\n";

    // Setup active Intake
    $intakeObj = Intake::where('is_active', true)->first();
    if (!$intakeObj) {
        $intakeObj = Intake::create(['name' => 'SEPTEMBER 2024', 'is_active' => true]);
    }
    $intake = $intakeObj->name;

    // Setup active Department
    $deptObj = Department::where('is_active', true)->first();
    if (!$deptObj) {
        $deptObj = Department::create(['name' => 'Computer Science', 'is_active' => true]);
    }
    $department = $deptObj->name;

    // Create a new pending application
    echo "\n📝 Creating pending Hostel Application...\n";
    $application = HostelApplication::create([
        'academic_year' => '2026/2027',
        'amount_paid' => 150000,
        'full_name' => 'Praize Tchris',
        'student_id' => 'LUC-NGA-002-ADM-' . mt_rand(1000000, 9999999), // Unique formatted Student ID
        'intake' => $intake,
        'program' => 'B.Sc. Computer Science',
        'department' => $department,
        'gender' => 'male',
        'date_of_birth' => '2002-05-15',
        'phone_number' => '+234 803 834 1496', // Unique phone number
        'email' => 'chimezietchris@gmail.com',
        'home_address' => '123 Test Street, Abuja',
        'nationality' => 'Nigerian',
        'state_of_origin' => 'Nasarawa',
        'local_government' => 'Keffi',
        'parent_full_name' => 'Guardian Tchris',
        'parent_relationship' => 'Father',
        'parent_phone' => '+234 803 834 1496',
        'parent_email' => 'chimezietchris@gmail.com',
        'parent_address' => '123 Test Street, Abuja',
        'parent_occupation' => 'Business',
        'emergency_contact_name' => 'Emergency Tchris',
        'emergency_contact_phone' => '+234 803 834 1496',
        'emergency_contact_relationship' => 'Uncle',
        'emergency_contact_address' => '123 Test Street, Abuja',
        'declaration_name' => 'Praize Tchris',
        'applicant_signature' => 'Praize Tchris',
        'applicant_date' => '2026-05-18',
        'guardian_signature' => 'Guardian Tchris',
        'guardian_date' => '2026-05-18',
        'status' => 'pending',
    ]);

    echo "✅ Success: Application created!\n";
    echo "  - Name: {$application->full_name}\n";
    echo "  - Application Number: {$application->application_number}\n";
    echo "  - Assigned Student ID (Admission Number): {$application->student_id}\n";
    echo "  - Email: {$application->email}\n";
    echo "  - Phone Number: {$application->phone_number}\n";

    // 4. Trigger Live Email Send
    echo "\n📧 Sending Application Received Confirmation Email to chimezietchris@gmail.com...\n";
    Mail::to($application->email)->send(new \App\Mail\ApplicationReceivedMail($application));
    echo "✅ Success: Confirmation Email successfully dispatched!\n\n";
    
    echo "🎉 READY FOR MANUAL APPROVAL:\n";
    echo "Please log into the Admin Dashboard, navigate to 'Hostel Applications', find the pending application for 'Praize Tchris' (ID: {$application->application_number}), and approve it to trigger the approval and room assignment notifications!\n";

} catch (\Exception $ex) {
    echo "\n❌ ERROR OCCURRED: " . $ex->getMessage() . "\n";
    echo "Line: " . $ex->getLine() . " in " . $ex->getFile() . "\n";
    exit(1);
}
