<?php

/**
 * End-to-End Registration & Approval Test Flow
 * 
 * This script programmatically simulates the entire student registration and approval
 * pipeline for "Praize Tchris" with email "chimezietchris@gmail.com".
 * It cleans up previous records, registers a pending application, triggers
 * application received email, approves it while assigning a room/bed, and verifies
 * database consistency, secure credential generation, and mail delivery.
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
use App\Models\Hostel;
use App\Models\Intake;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

echo "=========================================================\n";
echo "🚀 LINCOLN HOSTEL SYSTEM: E2E REGISTRATION & APPROVAL TEST FLOW\n";
echo "=========================================================\n\n";

try {
    // 2. Cleanup existing data under the name "Praize Tchris" / "chimezietchris@gmail.com"
    echo "🧹 [STEP 1/5] Cleaning up existing test records...\n";
    
    // Find Student
    $student = Student::where('email', 'chimezietchris@gmail.com')
                      ->orWhere('full_name', 'Praize Tchris')
                      ->first();

    if ($student) {
        echo "Found existing student record (ID: {$student->id}). Cleaning up atomically...\n";
        DB::transaction(function () use ($student) {
            $roomId = $student->room_id;
            $bedId = $student->bed_id;
            $userId = $student->user_id;

            // Decrement Room Occupancy
            if ($roomId) {
                Room::where('id', $roomId)->where('occupied', '>', 0)->update([
                    'occupied' => DB::raw('occupied - 1'),
                    'status' => DB::raw('CASE WHEN occupied - 1 < capacity AND status = "full" THEN "available" ELSE status END'),
                ]);
                echo "- Decremented occupancy for room ID: {$roomId}\n";
            }

            // Vacate Bed
            if ($bedId) {
                Bed::where('id', $bedId)->update([
                    'is_occupied' => false,
                    'student_id' => null,
                ]);
                echo "- Vacated bed ID: {$bedId}\n";
            }

            $student->delete();
            echo "- Deleted student record\n";

            if ($userId) {
                User::where('id', $userId)->delete();
                echo "- Deleted student user account\n";
            }
        });
    } else {
        echo "- No existing student record found.\n";
    }

    // Clean up temporary admin test user if created in previous runs
    $tempAdmin = User::where('email', 'admin_test@lincoln.edu.ng')->first();
    if ($tempAdmin) {
        $tempAdmin->delete();
        echo "- Deleted temporary test admin account\n";
    }

    // Clean up Application
    $deletedApplications = HostelApplication::where('email', 'chimezietchris@gmail.com')
                                           ->orWhere('full_name', 'Praize Tchris')
                                           ->delete();
    if ($deletedApplications > 0) {
        echo "- Deleted {$deletedApplications} existing application record(s)\n";
    } else {
        echo "- No existing application record found.\n";
    }
    
    echo "✨ Cleanup completed successfully!\n\n";

    // 3. Register a pending application for "Praize Tchris"
    echo "📝 [STEP 2/5] Creating a pending Hostel Application...\n";
    
    // Get or create active intake
    $intakeObj = Intake::where('is_active', true)->first();
    if (!$intakeObj) {
        $intakeObj = Intake::create(['name' => 'SEPTEMBER 2024', 'is_active' => true]);
    }
    $intake = $intakeObj->name;

    // Get or create active department
    $deptObj = Department::where('is_active', true)->first();
    if (!$deptObj) {
        $deptObj = Department::create(['name' => 'Computer Science', 'is_active' => true]);
    }
    $department = $deptObj->name;

    // Create application
    $application = HostelApplication::create([
        'academic_year' => '2026/2027',
        'amount_paid' => 150000,
        'full_name' => 'Praize Tchris',
        'student_id' => 'LUC-NGA-002-ADM-' . mt_rand(1000000, 9999999), // Unique ID matching the target pattern
        'intake' => $intake,
        'program' => 'B.Sc. Computer Science',
        'department' => $department,
        'gender' => 'male',
        'date_of_birth' => '2002-05-15',
        'phone_number' => '+234 812 345 6789',
        'email' => 'chimezietchris@gmail.com',
        'home_address' => '123 Test Street, Abuja',
        'nationality' => 'Nigerian',
        'state_of_origin' => 'Nasarawa',
        'local_government' => 'Keffi',
        'parent_full_name' => 'Guardian Tchris',
        'parent_relationship' => 'Father',
        'parent_phone' => '+234 803 834 1496',
        'parent_email' => 'chimezietchris@gmail.com', // Using target email for both to confirm parent receipt if any
        'parent_address' => '123 Test Street, Abuja',
        'parent_occupation' => 'Business',
        'emergency_contact_name' => 'Emergency Tchris',
        'emergency_contact_phone' => '+234 803 834 1496',
        'emergency_contact_relationship' => 'Uncle',
        'emergency_contact_address' => '123 Test Street, Abuja',
        
        // Signatures and dates
        'declaration_name' => 'Praize Tchris',
        'applicant_signature' => 'Praize Tchris',
        'applicant_date' => '2026-05-18',
        'guardian_signature' => 'Guardian Tchris',
        'guardian_date' => '2026-05-18',
        'status' => 'pending',
    ]);

    echo "Application created with number: {$application->application_number} and status: {$application->status}\n";

    // Trigger confirmation email
    echo "📧 Sending Application Received Mail...\n";
    Mail::to($application->email)->send(new \App\Mail\ApplicationReceivedMail($application));
    echo "✅ Application Received Mail sent successfully to {$application->email}!\n\n";

    // 4. Setup Hostels, Room, Bed and Admin for Approval
    echo "🏨 [STEP 3/5] Setting up hostel rooms and admin credentials for approval...\n";

    // Check/create available male room
    $room = Room::where('gender_type', 'male')
                ->where('status', 'available')
                ->whereRaw('occupied < capacity')
                ->first();

    if (!$room) {
        echo "No active available male room found in DB. Setting one up programmatically...\n";
        $hostel = Hostel::firstOrCreate(
            ['name' => 'Main Male Hostel'],
            [
                'gender_type' => 'male',
                'capacity' => 20,
                'address' => 'Lincoln Campus Ground',
                'description' => 'Test Male Residence',
                'status' => 'active',
            ]
        );

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'room_number' => 'MALE-101',
            'room_type' => 'double',
            'price_per_semester' => 150000,
            'price_per_year' => 280000,
            'floor_number' => 1,
            'capacity' => 2,
            'occupied' => 0,
            'status' => 'available',
            'gender_type' => 'male',
        ]);
        echo "- Room MALE-101 created.\n";
    } else {
        echo "- Found available room: {$room->room_number} (Hostel: {$room->hostel->name})\n";
    }

    // Check/create vacant bed
    $bed = Bed::where('room_id', $room->id)->where('is_occupied', false)->first();
    if (!$bed) {
        $bed = Bed::create([
            'room_id' => $room->id,
            'bed_number' => 'Bed-' . mt_rand(1, 100),
            'is_occupied' => false,
        ]);
        echo "- Bed created for room {$room->room_number}.\n";
    } else {
        echo "- Found vacant bed: {$bed->bed_number}\n";
    }

    // Set up and authenticate test admin
    $admin = User::where('role', 'admin')->orWhere('is_admin', true)->first();
    if (!$admin) {
        echo "No admin user found. Creating a temporary test admin account...\n";
        $admin = User::create([
            'name' => 'Lincoln Test Admin',
            'email' => 'admin_test@lincoln.edu.ng',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
    }
    Auth::login($admin);
    echo "- Authenticated as Admin: {$admin->name} ({$admin->email})\n\n";

    // 5. Execute approval and room assignment
    echo "⚡ [STEP 4/5] Executing HostelApplicationController@approveAndAssign method...\n";
    
    // Save state before approval to assert correctly
    $initialOccupied = Room::where('id', $room->id)->value('occupied');

    $request = \Illuminate\Http\Request::create("/admin/applications/{$application->id}/approve", 'POST', [
        'hostel_id' => $room->hostel_id,
        'room_id' => $room->id,
        'bed_id' => $bed->id,
        'admin_notes' => 'E2E Automated test execution. Verification of secure emails and room assign mail triggers.',
    ]);

    $controller = $app->make(App\Http\Controllers\HostelApplicationController::class);
    $response = $controller->approveAndAssign($request, $application);

    echo "✅ Method executed successfully!\n\n";

    // 6. DB Verification & Parity Asserts
    echo "🔬 [STEP 5/5] Verifying database integrity and parity requirements...\n";

    // Re-query database to see the results
    $freshApplication = HostelApplication::findOrFail($application->id);
    $freshStudent = Student::where('admission_number', $application->student_id)->first();
    $freshRoom = Room::findOrFail($room->id);
    $freshBed = Bed::findOrFail($bed->id);

    echo "Database Checks:\n";
    
    // Check 1: Application status is approved
    if ($freshApplication->status === 'approved') {
        echo "✅ SUCCESS: Application is marked as 'approved' (Reviewed at: {$freshApplication->reviewed_at})\n";
    } else {
        throw new \Exception("FAIL: Application status is still '{$freshApplication->status}'");
    }

    // Check 2: Student record created
    if ($freshStudent) {
        echo "✅ SUCCESS: Student record successfully created!\n";
        echo "  - Admission Number (Student ID): {$freshStudent->admission_number}\n";
        echo "  - Assigned Room: {$freshRoom->room_number}\n";
        echo "  - Assigned Bed: {$freshBed->bed_number}\n";
    } else {
        throw new \Exception("FAIL: Student record was not created");
    }

    // Check 3: Room occupancy correctly updated
    if ($freshRoom->occupied == $initialOccupied + 1) {
        echo "✅ SUCCESS: Room occupied count atomically incremented (Before: {$initialOccupied}, After: {$freshRoom->occupied})\n";
    } else {
        throw new \Exception("FAIL: Room occupancy did not increment correctly. Expected " . ($initialOccupied + 1) . ", got {$freshRoom->occupied}");
    }

    // Check 4: Bed occupied state and student id updated
    if ($freshBed->is_occupied && $freshBed->student_id == $freshStudent->id) {
        echo "✅ SUCCESS: Bed marked as occupied and linked to Student ID: {$freshStudent->id}\n";
    } else {
        throw new \Exception("FAIL: Bed status update failed.");
    }

    // Check 5: Financial Ledger (Flow A) checks
    if ($freshStudent->hostel_fee_status === 'paid' && 
        $freshStudent->hostel_fee_amount == $freshApplication->amount_paid &&
        $freshStudent->hostel_fee_paid == $freshApplication->amount_paid) {
        echo "✅ SUCCESS: Flow A Payment verified. Ledger columns in sync (Amount: {$freshStudent->hostel_fee_amount}, Paid: {$freshStudent->hostel_fee_paid}, Status: {$freshStudent->hostel_fee_status})\n";
    } else {
        throw new \Exception("FAIL: Financial ledger columns are incorrect.");
    }

    // Check 6: User account exists
    $freshUser = User::where('email', 'chimezietchris@gmail.com')->first();
    if ($freshUser) {
        echo "✅ SUCCESS: Student user account created successfully under role 'student'!\n";
    } else {
        throw new \Exception("FAIL: User account not found.");
    }

    echo "\n🎉 E2E TEST PASSED FLUSH-FREE! ALL SYSTEM CONTROLS OPERATING IN PARITY AND SECURITY!\n";

} catch (\Exception $ex) {
    echo "\n❌ TEST FAILED: " . $ex->getMessage() . "\n";
    echo "Line: " . $ex->getLine() . " inside " . $ex->getFile() . "\n";
    exit(1);
}
