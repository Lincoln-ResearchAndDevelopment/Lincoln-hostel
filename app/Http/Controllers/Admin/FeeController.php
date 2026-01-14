<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Hostel;
use App\Models\Student;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $hostels = Hostel::with(['rooms' => function($q) {
            $q->select('id', 'hostel_id', 'room_number', 'price_per_semester', 'price_per_year', 'room_type', 'capacity');
        }])->get();

        // Fee statistics
        $stats = [
            'total_expected' => Student::where('status', 'active')->sum('hostel_fee_amount'),
            'total_collected' => Student::where('status', 'active')->sum('hostel_fee_paid'),
            'total_outstanding' => Student::where('status', 'active')
                ->selectRaw('SUM(hostel_fee_amount - hostel_fee_paid) as outstanding')
                ->value('outstanding') ?? 0,
            'fully_paid' => Student::where('status', 'active')
                ->whereRaw('hostel_fee_paid >= hostel_fee_amount')
                ->count(),
            'partial_paid' => Student::where('status', 'active')
                ->whereRaw('hostel_fee_paid > 0 AND hostel_fee_paid < hostel_fee_amount')
                ->count(),
            'not_paid' => Student::where('status', 'active')
                ->where('hostel_fee_paid', 0)
                ->count(),
        ];

        return view('admin.fees.index', compact('hostels', 'stats'));
    }

    public function updateRoomPrice(Request $request, Room $room)
    {
        $request->validate([
            'price_per_semester' => 'required|numeric|min:0',
            'price_per_year' => 'nullable|numeric|min:0',
        ]);

        $room->update([
            'price_per_semester' => $request->price_per_semester,
            'price_per_year' => $request->price_per_year ?? ($request->price_per_semester * 2),
        ]);

        return redirect()->back()->with('success', 'Room price updated successfully.');
    }

    public function studentFees(Request $request)
    {
        $query = Student::with('room.hostel', 'payments');

        // Filter by payment status
        if ($request->has('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'paid':
                    $query->whereRaw('hostel_fee_paid >= hostel_fee_amount');
                    break;
                case 'partial':
                    $query->whereRaw('hostel_fee_paid > 0 AND hostel_fee_paid < hostel_fee_amount');
                    break;
                case 'unpaid':
                    $query->where('hostel_fee_paid', 0);
                    break;
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $students = $query->where('status', 'active')->paginate(20);

        return view('admin.fees.students', compact('students'));
    }

    public function updateStudentFee(Request $request, Student $student)
    {
        $request->validate([
            'hostel_fee_amount' => 'required|numeric|min:0',
            'payment_due_date' => 'nullable|date',
        ]);

        $student->update([
            'hostel_fee_amount' => $request->hostel_fee_amount,
            'payment_due_date' => $request->payment_due_date,
        ]);

        return redirect()->back()->with('success', 'Student fee updated successfully.');
    }
}
