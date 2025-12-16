<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class StudentsDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

   public function index()
{
    $student = auth()->guard('student')->user();

    $complaints = $student->complaints; // or Complaint::where('student_id', $student->id)->get();

    $latestAnnouncements = Announcement::orderBy('created_at', 'desc')->take(5)->get();
    $unreadAnnouncements = Announcement::count();

    return view('student.dashboard', compact(
        'student',
        'latestAnnouncements',
        'unreadAnnouncements',
        'complaints'
    ));
}

}
