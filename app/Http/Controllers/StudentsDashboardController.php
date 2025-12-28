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

    $complaints = $student->complaints; // student's complaints

    // Fetch payments for payment history
    $payments = $student->payments()->latest()->get();

    $latestAnnouncements = Announcement::orderBy('created_at', 'desc')->take(5)->get();
    $unreadAnnouncements = Announcement::count();

    // Dashboard summary stats for the student overview
    $total_payments = $payments->count();
    $total_paid = $payments->where('status', 'completed')->sum('amount');
    $pending_payments = $payments->where('status', 'pending')->count();

    $total_complaints = $complaints->count();
    $pending_complaints = $complaints->whereIn('status', ['submitted', 'in progress'])->count();

    return view('student.dashboard', compact(
        'student',
        'latestAnnouncements',
        'unreadAnnouncements',
        'complaints',
        'payments',
        'total_payments',
        'total_paid',
        'pending_payments',
        'total_complaints',
        'pending_complaints'
    ));
}

}
