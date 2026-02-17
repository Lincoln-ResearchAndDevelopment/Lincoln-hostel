<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\User;
use App\Mail\LeaveRequestSubmittedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;

class StudentLeaveController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $leaveRequests = $student->leaveRequests()->latest()->paginate(10);
        
        return view('student.leave.index', compact('leaveRequests'));
    }

    public function create()
    {
        return view('student.leave.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:medical,home,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'emergency_contact' => 'nullable|string|max:20',
            'destination' => 'nullable|string|max:255',
        ]);

        $student = Auth::guard('student')->user();

        $leaveRequest = LeaveRequest::create([
            'student_id' => $student->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'emergency_contact' => $request->emergency_contact,
            'destination' => $request->destination,
            'status' => 'pending',
        ]);

        // Load the student relationship
        $leaveRequest->load('student');

        // Send email to admin(s)
        $this->notifyAdmins($leaveRequest);

        // Send email to parent/guardian
        $this->notifyParent($leaveRequest);

        // In-app notification for all admins
        try {
            $msg = $leaveRequest->student->full_name . ' submitted a leave request (' . $leaveRequest->type . ') from ' . $leaveRequest->start_date->format('M d') . ' to ' . $leaveRequest->end_date->format('M d, Y') . '.';
            Notification::notifyAllAdmins('leave_submitted', 'New Leave Request', $msg, ['leave_request_id' => $leaveRequest->id]);
        } catch (\Exception $e) {
            Log::error('Failed to create in-app leave notification for admins: ' . $e->getMessage());
        }

        return redirect()->route('student.leave.index')
            ->with('success', 'Leave request submitted successfully and has been sent to the admin for approval.');
    }

    /**
     * Notify all admins about the new leave request
     */
    private function notifyAdmins(LeaveRequest $leaveRequest)
    {
        try {
            // Get all admin users
            $admins = User::where('role', 'admin')->orWhere('is_admin', true)->get();
            
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new LeaveRequestSubmittedMail($leaveRequest, 'admin'));
            }

            // If no specific admins found, send to a default admin email
            if ($admins->isEmpty()) {
                $defaultAdminEmail = config('mail.admin_email', 'admin@linchostel.com');
                Mail::to($defaultAdminEmail)->send(new LeaveRequestSubmittedMail($leaveRequest, 'admin'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send leave request notification to admin: ' . $e->getMessage());
        }
    }

    /**
     * Notify parent/guardian about the leave request
     */
    private function notifyParent(LeaveRequest $leaveRequest)
    {
        try {
            $student = $leaveRequest->student;
            $parentEmail = $student->parent_email;
            $parentPhone = $student->parent_phone;

            // Fallback to application data if student profile is incomplete
            if (($this->isMissing($parentEmail) || $this->isMissing($parentPhone)) && $student->hostelApplication) {
                if ($this->isMissing($parentEmail)) {
                    $parentEmail = $student->hostelApplication->parent_email;
                }
                if ($this->isMissing($parentPhone)) {
                    $parentPhone = $student->hostelApplication->parent_phone;
                }
                
                // Also try to patch missing name for the email template
                if (empty($student->parent_name)) {
                    $student->parent_name = $student->hostelApplication->parent_full_name;
                }
            }
            
            // Send Email
            if (!empty($parentEmail)) {
                Log::info("Sending leave request email to parent: {$parentEmail}");
                Mail::to($parentEmail)->send(new LeaveRequestSubmittedMail($leaveRequest, 'parent'));
            } else {
                Log::warning("No parent email found for student ID {$student->id} during leave request notification.");
            }

            // Send SMS
            if (!empty($parentPhone)) {
                Log::info("Sending leave request SMS to parent: {$parentPhone}");
                $smsService = new SmsService();
                $message = "LincHostel: Your ward {$student->full_name} has applied for leave ({$leaveRequest->type}) from {$leaveRequest->start_date->format('d/m/Y')} to {$leaveRequest->end_date->format('d/m/Y')}. Reason: " . \Illuminate\Support\Str::limit($leaveRequest->reason, 40);
                $smsService->sendSms($parentPhone, $message);
            } else {
                Log::warning("No parent phone found for student ID {$student->id} during leave request notification.");
            }

        } catch (\Exception $e) {
            Log::error('Failed to send leave request notification to parent: ' . $e->getMessage());
        }
    }

    /**
     * Helper to check if a value is effectively empty
     */
    private function isMissing($value)
    {
        return empty($value) || $value === 'N/A';
    }
}
