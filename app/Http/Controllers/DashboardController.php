<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Visitor;
use App\Models\Complaint;
use App\Models\Announcement;
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
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->whereRaw('occupied < capacity')->count(),
            'total_students' => Student::where('status', 'active')->count(),
            'pending_complaints' => Complaint::whereIn('status', ['submitted', 'in progress'])->count(),
            'recent_payments' => Payment::with('student')->latest()->take(5)->get(),
            'recent_visitors' => Visitor::with('student')->whereNull('check_out_time')->latest()->take(5)->get(),
            'announcements' => Announcement::latest()->take(7)->get(),
            'allAnnouncements' => Announcement::latest()->get(), 
        ];

        return view('dashboard', $data);
    }
}