<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\LeaveRequest;
use App\Mail\LeaveRequestSubmittedMail;
use App\Mail\LeaveStatusUpdateMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "=== LEAVE REQUEST COMMUNICATIONS TEST ===\n";

try {
    // 1. Find the test student
    $student = Student::find(110);
    if (!$student) {
        throw new \Exception("Test student with ID 110 not found!");
    }

    echo "Found student: {$student->full_name} (Email: {$student->email})\n";

    // 2. Fetch or create a pending leave request
    $leaveRequest = LeaveRequest::where('student_id', $student->id)->latest()->first();
    if (!$leaveRequest) {
        echo "Creating a temporary test leave request...\n";
        $leaveRequest = LeaveRequest::create([
            'student_id' => $student->id,
            'type' => 'home',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'reason' => 'Testing new student confirmation and parent greeting systems.',
            'emergency_contact' => '08038341496',
            'destination' => 'Enugu',
            'status' => 'pending',
        ]);
    } else {
        echo "Using existing leave request (ID: {$leaveRequest->id}, Status: {$leaveRequest->status})\n";
    }

    $leaveRequest->load('student');

    // 3. Dispatch Submission Confirmation to Student
    echo "Sending LeaveRequestSubmittedMail to Student...\n";
    Mail::to($student->email)->send(new LeaveRequestSubmittedMail($leaveRequest, 'student'));
    echo "SUCCESS: Student submission confirmation email dispatched!\n\n";

    // 4. Dispatch Submission Notification to Parent
    $parentEmail = $student->parent_email ?: 'chimezietchris@gmail.com';
    echo "Sending LeaveRequestSubmittedMail to Parent (Email: {$parentEmail})...\n";
    Mail::to($parentEmail)->send(new LeaveRequestSubmittedMail($leaveRequest, 'parent'));
    echo "SUCCESS: Parent submission notification email dispatched!\n\n";

    // 5. Force-mock the status to 'rejected' to test rejection mail templates
    $rejectedLeave = clone $leaveRequest;
    $rejectedLeave->status = 'rejected';
    $rejectedLeave->rejection_reason = 'Academic calendar constraints and pending mid-semester exams.';

    // 6. Dispatch Rejection Update to Parent (greeted by name)
    echo "Sending LeaveStatusUpdateMail (Rejected) to Parent (Email: {$parentEmail})...\n";
    Mail::to($parentEmail)->send(new LeaveStatusUpdateMail($rejectedLeave, 'parent'));
    echo "SUCCESS: Parent rejection notification email dispatched!\n\n";

    // 7. Dispatch Rejection Update to Student
    echo "Sending LeaveStatusUpdateMail (Rejected) to Student (Email: {$student->email})...\n";
    Mail::to($student->email)->send(new LeaveStatusUpdateMail($rejectedLeave, 'student'));
    echo "SUCCESS: Student rejection notification email dispatched!\n\n";

    echo "=== ALL COMMUNICATIVE CHANNELS DISPATCHED SUCCESSFULLY ===\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
}
