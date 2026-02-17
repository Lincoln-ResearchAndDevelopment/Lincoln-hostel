<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\HostelRule;
use App\Models\AttendanceRecord;
use App\Models\Notification;

class StudentsDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    /**
     * Student Dashboard - Main Overview
     */
    public function index()
    {
        $student = auth()->guard('student')->user();

        // Load relationships
        $student->load(['room.hostel', 'payments', 'complaints', 'leaveRequests', 'attendanceRecords']);

        $complaints = $student->complaints;
        $payments = $student->payments()->latest()->get();
        $leaveRequests = $student->leaveRequests()->latest()->take(5)->get();

        // Announcements
        $gender = $student->gender; // Male or Female
        $latestAnnouncements = Announcement::whereIn('target_audience', ['General', $gender])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $unreadAnnouncements = Announcement::whereIn('target_audience', ['General', $gender])->count();

        // Unread Notifications
        $unreadNotifications = $student->notifications()->unread()->latest()->take(5)->get();

        // Dashboard summary stats
        $total_payments = $payments->count();
        $total_paid = $payments->where('status', 'completed')->sum('amount');
        $pending_payments = $payments->where('status', 'pending')->count();

        $total_complaints = $complaints->count();
        $pending_complaints = $complaints->whereIn('status', ['submitted', 'in progress'])->count();

        // Attendance records (last 7 days)
        $recentAttendance = $student->attendanceRecords()
            ->where('recorded_at', '>=', now()->subDays(7))
            ->orderBy('recorded_at', 'desc')
            ->take(10)
            ->get();

        // Fee information
        $feeInfo = [
            'total_fee' => $student->hostel_fee_amount,
            'paid' => $student->hostel_fee_paid,
            'outstanding' => $student->outstanding_balance,
            'status' => $student->hostel_fee_status,
            'due_date' => $student->payment_due_date,
            'is_overdue' => $student->isPaymentOverdue(),
        ];

        // Check for pending room booking
        $pendingBooking = $student->payments()
            ->where('status', 'pending')
            ->whereNotNull('room_id')
            ->with(['room.hostel'])
            ->latest()
            ->first();

        return view('student.dashboard', compact(
            'student',
            'latestAnnouncements',
            'unreadAnnouncements',
            'unreadNotifications',
            'complaints',
            'payments',
            'leaveRequests',
            'total_payments',
            'total_paid',
            'pending_payments',
            'total_complaints',
            'pending_complaints',
            'recentAttendance',
            'feeInfo',
            'pendingBooking'
        ));
    }

    /**
     * View Personal Profile
     */
    public function profile()
    {
        $student = auth()->guard('student')->user();
        $student->load(['room.hostel', 'hostelApplication']);
        
        return view('student.profile.index', compact('student'));
    }

    /**
     * View Room & Hostel Allocation Details
     */
    public function roomDetails()
    {
        $student = auth()->guard('student')->user();
        $student->load(['room.hostel', 'room.students']);
        
        if (!$student->room) {
            return redirect()->route('student.dashboard')
                ->with('info', 'You do not have a room assigned yet.');
        }

        $room = $student->room;
        $hostel = $room->hostel;
        $roommates = $room->students->where('id', '!=', $student->id);

        return view('student.room.details', compact('student', 'room', 'hostel', 'roommates'));
    }

    /**
     * View Hostel Rules & Regulations
     */
    public function hostelRules()
    {
        $student = auth()->guard('student')->user();
        $hostelId = $student->room ? $student->room->hostel_id : null;

        // Get rules applicable to student's hostel (or all global rules)
        $rules = HostelRule::active()
            ->when($hostelId, function($query) use ($hostelId) {
                return $query->forHostel($hostelId);
            }, function($query) {
                return $query->global();
            })
            ->orderBy('category')
            ->orderBy('order')
            ->get()
            ->groupBy('category');

        $categories = HostelRule::getCategories();

        return view('student.hostel.rules', compact('rules', 'categories', 'student'));
    }

    /**
     * View Announcements & Notices
     */
    public function announcements()
    {
        $student = auth()->guard('student')->user();
        $gender = $student->gender;

        $announcements = Announcement::whereIn('target_audience', ['General', $gender])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('student.announcements.index', compact('announcements'));
    }

    /**
     * View Single Announcement
     */
    public function showAnnouncement(Announcement $announcement)
    {
        $student = auth()->guard('student')->user();
        $gender = $student->gender;

        if (!in_array($announcement->target_audience, ['General', $gender])) {
            return redirect()->route('student.announcements.index')
                ->with('error', 'You are not authorized to view this announcement.');
        }

        return view('student.announcements.show', compact('announcement'));
    }

    /**
     * View Attendance / In-Out Records
     */
    public function attendance(Request $request)
    {
        $student = auth()->guard('student')->user();
        
        // Filter by date range
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $attendanceRecords = $student->attendanceRecords()
            ->whereBetween('recorded_at', [$startDate, $endDate . ' 23:59:59'])
            ->orderBy('recorded_at', 'desc')
            ->paginate(20);

        // Summary stats
        $checkIns = $student->attendanceRecords()
            ->whereBetween('recorded_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('type', 'check_in')
            ->count();

        $checkOuts = $student->attendanceRecords()
            ->whereBetween('recorded_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('type', 'check_out')
            ->count();

        return view('student.attendance.index', compact(
            'student',
            'attendanceRecords',
            'startDate',
            'endDate',
            'checkIns',
            'checkOuts'
        ));
    }

    /**
     * View Hostel Fee Details & Payment Status
     */
    public function feeDetails()
    {
        $student = auth()->guard('student')->user();
        $student->load('payments');

        $payments = $student->payments()->orderBy('created_at', 'desc')->get();

        $feeInfo = [
            'total_fee' => $student->hostel_fee_amount,
            'paid' => $student->hostel_fee_paid,
            'outstanding' => $student->outstanding_balance,
            'status' => $student->hostel_fee_status,
            'due_date' => $student->payment_due_date,
            'is_overdue' => $student->isPaymentOverdue(),
            'payment_percentage' => $student->payment_percentage,
        ];

        return view('student.fees.index', compact('student', 'payments', 'feeInfo'));
    }

    /**
     * View Complaints History & Status
     */
    public function complaints()
    {
        $student = auth()->guard('student')->user();
        $complaints = $student->complaints()->orderBy('created_at', 'desc')->paginate(10);

        return view('student.complaints.index', compact('student', 'complaints'));
    }

    /**
     * View Single Complaint Details
     */
    public function showComplaint($id)
    {
        $student = auth()->guard('student')->user();
        $complaint = $student->complaints()->findOrFail($id);

        return view('student.complaints.show', compact('student', 'complaint'));
    }

    /**
     * View Leave Requests History & Status
     */
    public function leaveRequests()
    {
        $student = auth()->guard('student')->user();
        $leaveRequests = $student->leaveRequests()->orderBy('created_at', 'desc')->paginate(10);

        return view('student.leave.index', compact('student', 'leaveRequests'));
    }

    /**
     * Browse Available Hostels
     */
    public function hostels(Request $request)
    {
        $student = auth()->guard('student')->user();
        $gender = strtolower($student->gender);
        
        $query = \App\Models\Hostel::where('status', 'active');
        
        // Filter by student gender
        $query->where(function($q) use ($gender) {
            $q->where('type', $gender)
              ->orWhere('type', 'mixed');
        });

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && in_array($request->type, ['male', 'female', 'mixed'])) {
            // Further restrict if they try to filter for a type they shouldn't see
            $requestedType = $request->type;
            if ($requestedType !== 'mixed' && $requestedType !== $gender) {
                return redirect()->route('student.hostels.index')->with('error', 'You can only view hostels matching your gender.');
            }
            $query->where('type', $requestedType);
        }

        $hostels = $query->withCount(['rooms' => function($q) {
            $q->where('status', 'available');
        }])->get();

        return view('student.hostels.index', compact('hostels'));
    }

    /**
     * View Single Hostel Details
     */
    public function showHostel(\App\Models\Hostel $hostel, Request $request)
    {
        $student = auth()->guard('student')->user();
        $gender = strtolower($student->gender);
        $roomType = $request->get('room_type');

        // Security check: Ensure student matches hostel type
        if ($hostel->type !== 'mixed' && $hostel->type !== $gender) {
            return redirect()->route('student.hostels.index')->with('error', 'You cannot view this hostel as it is not designated for your gender.');
        }

        $rooms = $hostel->rooms()
            ->where('gender_type', $gender)
            ->where('status', 'available')
            ->whereRaw('occupied < capacity')
            ->when($roomType, function($query) use ($roomType) {
                return $query->where('room_type', $roomType);
            })
            ->orderBy('room_number')
            ->get();

        return view('student.hostels.show', compact('hostel', 'rooms'));
    }

    /**
     * Book a Room
     */
    public function bookRoom(Request $request, \App\Models\Room $room)
    {
        $student = auth()->guard('student')->user();

        // Validation
        $request->validate([
            'payment_plan' => 'required|in:semester,year',
        ]);

        $gender = strtolower($student->gender);
        $hostel = $room->hostel;

        // Check 1: Gender compatibility
        if ($hostel->type !== 'mixed' && $hostel->type !== $gender) {
            return back()->with('error', 'You cannot book a room in this hostel as it is not designated for your gender.');
        }

        if ($room->gender_type !== $gender) {
            return back()->with('error', 'This room is designated for ' . $room->gender_type . ' students only.');
        }

        // Check 2: Room status
        if ($room->status !== 'available') {
            return back()->with('error', 'This room is currently not available for booking.');
        }

        // Check 3: Room capacity - CRITICAL: Refresh from database to get latest occupancy
        $room->refresh();
        $remainingSlots = $room->capacity - $room->occupied;
        
        if ($room->occupied >= $room->capacity) {
            return back()->with('error', 'Sorry! This room was just filled. Please select another room.');
        }

        // Check 4: Student already has a room
        if ($student->room_id) {
            return back()->with('error', 'You already have a room assigned. Please contact admin if you need to change rooms.');
        }

        // Check 5: Student already has a pending booking
        $pendingBooking = \App\Models\Payment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->whereNotNull('room_id')
            ->exists();
            
        if ($pendingBooking) {
            return back()->with('error', 'You already have a pending room booking. Please wait for admin approval.');
        }

        // All checks passed - proceed to payment
        return redirect()->route('student.rooms.booking_payment', [
            'room' => $room->id,
            'plan' => $request->payment_plan
        ])->with('info', "Great choice! Room has {$remainingSlots} slot(s) remaining. Complete payment to secure your spot.");
    }

    /**
     * Show booking payment page
     */
    public function showBookingPayment(\App\Models\Room $room, Request $request)
    {
        $student = auth()->guard('student')->user();
        $plan = $request->query('plan', 'semester');
        
        if (!in_array($plan, ['semester', 'year'])) {
            $plan = 'semester';
        }

        $amount = ($plan === 'semester') ? $room->price_per_semester : $room->price_per_year;

        return view('student.hostels.booking_payment', compact('room', 'plan', 'amount', 'student'));
    }

    /**
     * Submit booking payment receipt
     */
    public function submitBookingPayment(Request $request, \App\Models\Room $room)
    {
        $student = auth()->guard('student')->user();

        $request->validate([
            'payment_plan' => 'required|in:semester,year',
            'amount' => 'required|numeric',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        // Create the payment record linked to the room
        $payment = \App\Models\Payment::create([
            'student_id' => $student->id,
            'room_id' => $room->id,
            'payment_plan' => $request->payment_plan,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'status' => 'pending',
            'payment_date' => now(),
            'receipt_number' => 'BR-'.strtoupper(\Illuminate\Support\Str::random(10)),
        ]);

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $fileName = 'booking_receipt_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('receipts', $fileName, 'public');
            
            $payment->receipt_path = $filePath;
            $payment->save();
        }

        // Notify Admins with detailed information
        \App\Models\Notification::notifyAllAdmins(
            'payment',
            '🏠 New Room Booking Payment Received',
            "Student: {$student->full_name} ({$student->admission_number})\n" .
            "Room: {$room->room_number} in {$room->hostel->name}\n" .
            "Payment Plan: " . ucfirst($request->payment_plan) . "\n" .
            "Amount: ₦" . number_format($payment->amount, 2) . "\n" .
            "Current Occupancy: {$room->occupied}/{$room->capacity}\n" .
            "Action Required: Review and approve this booking payment.",
            ['payment_id' => $payment->id, 'student_id' => $student->id, 'room_id' => $room->id]
        );

        // Notify Student that request is pending
        \App\Models\Notification::notifyStudent(
            $student->id,
            'payment',
            'Room Booking Submitted 🏠',
            "Your booking request for Room {$room->room_number} in {$room->hostel->name} has been received. " .
            "We are currently verifying your payment of ₦" . number_format($payment->amount, 2) . ". " .
            "You will be notified once an admin approves your booking and assigns your room.",
            ['payment_id' => $payment->id, 'room_id' => $room->id]
        );

        return redirect()->route('student.dashboard')
            ->with('success', 'Booking payment submitted successfully! Your room will be assigned once the admin approves your payment. We will notify you.');
    }

    /**
     * View Application Details (if student came from application)
     */
    public function applicationDetails()
    {
        $student = auth()->guard('student')->user();
        
        if (!$student->application_id) {
            return redirect()->route('student.dashboard')
                ->with('info', 'No application record found.');
        }

        $application = $student->hostelApplication;

        return view('student.application.details', compact('student', 'application'));
    }
}