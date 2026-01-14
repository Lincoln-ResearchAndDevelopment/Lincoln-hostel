<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Complaint;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function occupancy(Request $request)
    {
        $hostels = Hostel::with(['rooms' => function($q) {
            $q->withCount('students');
        }])->get();

        $stats = [];
        foreach ($hostels as $hostel) {
            $totalCapacity = $hostel->rooms->sum('capacity');
            $totalOccupied = $hostel->rooms->sum('occupied');
            $stats[$hostel->id] = [
                'name' => $hostel->name,
                'total_rooms' => $hostel->rooms->count(),
                'total_capacity' => $totalCapacity,
                'total_occupied' => $totalOccupied,
                'available' => $totalCapacity - $totalOccupied,
                'occupancy_rate' => $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 1) : 0,
            ];
        }

        return view('admin.reports.occupancy', compact('hostels', 'stats'));
    }

    public function fees(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $payments = Payment::with('student')
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->get();

        $stats = [
            'total_collected' => $payments->sum('amount'),
            'total_transactions' => $payments->count(),
            'by_method' => $payments->groupBy('payment_method')->map->sum('amount'),
        ];

        // Outstanding fees
        $outstandingStudents = Student::where('status', 'active')
            ->whereRaw('hostel_fee_paid < hostel_fee_amount')
            ->with('room.hostel')
            ->get();

        $totalOutstanding = $outstandingStudents->sum(function($s) {
            return $s->hostel_fee_amount - $s->hostel_fee_paid;
        });

        return view('admin.reports.fees', compact('payments', 'stats', 'outstandingStudents', 'totalOutstanding', 'startDate', 'endDate'));
    }

    public function complaints(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $complaints = Complaint::with('student')
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->get();

        $stats = [
            'total' => $complaints->count(),
            'by_status' => $complaints->groupBy('status')->map->count(),
            'resolved' => $complaints->where('status', 'resolved')->count(),
            'pending' => $complaints->whereIn('status', ['submitted', 'in progress'])->count(),
        ];

        return view('admin.reports.complaints', compact('complaints', 'stats', 'startDate', 'endDate'));
    }
}
