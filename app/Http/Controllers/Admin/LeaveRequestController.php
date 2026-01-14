<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Student;
use App\Mail\LeaveStatusUpdateMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = LeaveRequest::with('student');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->where('start_date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->where('end_date', '<=', $request->to_date);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Stats
        $stats = [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.leave.index', compact('leaveRequests', 'stats'));
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load('student.room.hostel');
        return view('admin.leave.show', compact('leaveRequest'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Load student relationship
        $leaveRequest->load('student');

        // Create dashboard notification for student
        $this->createStudentNotification($leaveRequest, 'approved');

        // Send email notifications
        $this->sendStatusUpdateEmails($leaveRequest);

        return redirect()->back()->with('success', 'Leave request approved successfully. Student and parent have been notified.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Load student relationship
        $leaveRequest->load('student');

        // Create dashboard notification for student
        $this->createStudentNotification($leaveRequest, 'rejected');

        // Send email notifications
        $this->sendStatusUpdateEmails($leaveRequest);

        return redirect()->back()->with('success', 'Leave request rejected. Student and parent have been notified.');
    }

    /**
     * Create a dashboard notification for the student
     */
    private function createStudentNotification(LeaveRequest $leaveRequest, string $status)
    {
        $student = $leaveRequest->student;
        
        if ($status === 'approved') {
            $title = 'Leave Request Approved';
            $message = "Your leave request from {$leaveRequest->start_date->format('M d')} to {$leaveRequest->end_date->format('M d, Y')} has been approved.";
            $type = 'leave_approved';
        } else {
            $title = 'Leave Request Rejected';
            $message = "Your leave request from {$leaveRequest->start_date->format('M d')} to {$leaveRequest->end_date->format('M d, Y')} has been rejected.";
            $type = 'leave_rejected';
        }

        Notification::notifyStudent($student->id, $type, $title, $message, [
            'leave_request_id' => $leaveRequest->id,
            'status' => $status,
            'rejection_reason' => $leaveRequest->rejection_reason,
        ]);
    }

    /**
     * Send email notifications to student and parent
     */
    private function sendStatusUpdateEmails(LeaveRequest $leaveRequest)
    {
        $student = $leaveRequest->student;

        // Send to student
        try {
            if ($student->email) {
                Mail::to($student->email)->send(new LeaveStatusUpdateMail($leaveRequest, 'student'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send leave status email to student: ' . $e->getMessage());
        }

        // Send to parent/guardian
        try {
            if ($student->parent_email) {
                Mail::to($student->parent_email)->send(new LeaveStatusUpdateMail($leaveRequest, 'parent'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send leave status email to parent: ' . $e->getMessage());
        }
    }
}
