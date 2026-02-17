<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Visitor;
use App\Models\Complaint;
use App\Models\Announcement;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function index()
    {
        $data = [
            'total_hostels' => Hostel::count(),
            'active_hostels' => Hostel::where('status', 'active')->count(),
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->whereRaw('occupied < capacity')->count(),
            'total_students' => Student::where('status', 'active')->count(),
            'pending_complaints' => Complaint::whereIn('status', ['submitted', 'in progress'])->count(),
            'recent_payments' => Payment::with('student')->latest()->take(5)->get(),
            'recent_visitors' => Visitor::with('student')->whereNull('check_out_time')->latest()->take(5)->get(),
            'announcements' => Announcement::latest()->take(7)->get(),
            'allAnnouncements' => Announcement::latest()->get(), 
            'latest_hostels' => Hostel::withCount('rooms')->latest()->take(4)->get(),
            
            // Hostel Applications Statistics
            'total_applications' => \App\Models\HostelApplication::count(),
            'pending_applications' => \App\Models\HostelApplication::where('status', 'pending')->count(),
            'approved_applications' => \App\Models\HostelApplication::where('status', 'approved')->count(),
            'rejected_applications' => \App\Models\HostelApplication::where('status', 'rejected')->count(),
            'recent_applications' => \App\Models\HostelApplication::latest()->take(5)->get(),
            
            
            // Leave Requests
            'pending_leave' => LeaveRequest::where('status', 'pending')->count(),
            
            // Recent Complaints
            'recent_complaints' => Complaint::with('student')->latest()->take(5)->get(),
            
            // Pending Room Bookings (NEW)
            'pending_bookings' => Payment::with(['student', 'room.hostel'])
                ->where('status', 'pending')
                ->whereNotNull('room_id')
                ->latest()
                ->take(10)
                ->get(),
            'pending_bookings_count' => Payment::where('status', 'pending')
                ->whereNotNull('room_id')
                ->count(),
        ];

        return view('admin.dashboard', $data);
    }
}