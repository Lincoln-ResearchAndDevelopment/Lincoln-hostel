<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Complaint;
use App\Models\Announcement; 

class StudentsDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    public function index()
    {
        $student = auth('student')->user();

        if (!$student->room_id) {
            return redirect()->back()->with('error', 'You have not been assigned a room yet.');
        }

        // Load payments and complaints with the student
        $payments = $student->payments()->latest()->get();
        $complaints = $student->complaints()->latest()->get();

        // Get unread announcements (global)
        $unreadAnnouncements = Announcement::count();
        $latestAnnouncements = Announcement::orderBy('created_at', 'desc')->take(5)->get();

        // Load the student's room (with its students)
        $room = $student->room()->with('students')->first();

        return view('student.dashboard', compact(
            'student',
            'payments',
            'complaints',
            'unreadAnnouncements',
            'latestAnnouncements',
            'room'
        ));
    }

}
