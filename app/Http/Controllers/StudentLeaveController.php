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

        return redirect()->route('student.leave.index')
            ->with('success', 'Leave request submitted successfully. Admin and your parent/guardian have been notified.');
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
            
            // Check if parent email exists
            if ($student->parent_email) {
                Mail::to($student->parent_email)->send(new LeaveRequestSubmittedMail($leaveRequest, 'parent'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send leave request notification to parent: ' . $e->getMessage());
        }
    }
}
