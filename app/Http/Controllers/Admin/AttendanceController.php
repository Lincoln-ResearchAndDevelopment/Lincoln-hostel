<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = AttendanceRecord::with('student.room.hostel');

        // Filter by date
        $date = $request->get('date', now()->format('Y-m-d'));
        $query->whereDate('recorded_at', $date);

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Search student
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('recorded_at', 'desc')->paginate(20);

        // Stats for today
        $stats = [
            'total_check_ins' => AttendanceRecord::whereDate('recorded_at', $date)->where('type', 'check_in')->count(),
            'total_check_outs' => AttendanceRecord::whereDate('recorded_at', $date)->where('type', 'check_out')->count(),
            'students_in' => Student::where('status', 'active')->whereHas('attendanceRecords', function($q) use ($date) {
                $q->whereDate('recorded_at', $date)->where('type', 'check_in');
            })->count(),
        ];

        return view('admin.attendance.index', compact('records', 'stats', 'date'));
    }

    public function create()
    {
        $students = Student::where('status', 'active')->whereNotNull('room_id')->orderBy('full_name')->get();
        return view('admin.attendance.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:check_in,check_out',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        AttendanceRecord::create([
            'student_id' => $request->student_id,
            'type' => $request->type,
            'recorded_at' => now(),
            'recorded_by' => auth()->user()->name,
            'location' => $request->location ?? 'Main Gate',
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance recorded successfully.');
    }

    public function studentHistory(Student $student, Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $records = $student->attendanceRecords()
            ->whereBetween('recorded_at', [$startDate, $endDate . ' 23:59:59'])
            ->orderBy('recorded_at', 'desc')
            ->paginate(20);

        return view('admin.attendance.student-history', compact('student', 'records', 'startDate', 'endDate'));
    }
}
