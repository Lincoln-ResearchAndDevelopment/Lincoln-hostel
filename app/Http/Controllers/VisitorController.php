<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VisitorController extends Controller
{
   public function index(Request $request)
    {
        $search = $request->get('search');

        if (Auth::user()->role === 'student') {
            $student = Student::where('user_id', Auth::id())->first();
            $visitors = Visitor::where('student_id', $student->id)
                ->when($search, function($query) use ($search) {
                    return $query->where('visitor_name', 'like', "%{$search}%")
                                ->orWhereHas('student', function($query) use ($search) {
                                    $query->where('full_name', 'like', "%{$search}%");
                                });
                })
                ->latest()
                ->paginate(10);
        } else {
            // Admin sees all visitors
            $visitors = Visitor::with('student')
                ->when($search, function($query) use ($search) {
                    return $query->where('visitor_name', 'like', "%{$search}%")
                                ->orWhereHas('student', function($query) use ($search) {
                                    $query->where('full_name', 'like', "%{$search}%");
                                });
                })
                ->latest()
                ->paginate(10);

            // Mark unread visitors as read (only for admin)
            Visitor::where('is_read', false)->update(['is_read' => true]);
        }

        return view('visitors.index', compact('visitors'));
    }

    public function create()
    {
        // For students, we'll auto-fill their ID
        if (Auth::user()->role === 'student') {
            $student = Student::where('user_id', Auth::id())->first();
            return view('visitors.create', compact('student'));
        }

        // For admin, show dropdown of students
        $students = Student::where('status', 'active')->get();
        return view('visitors.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'visitor_name' => 'required|string|max:255',
            'id_number' => 'required|string|max:255',
            'purpose' => 'required|string',
            'check_in_time' => 'nullable|date', // Added validation
        ]);

        // Set check_in_time to current time if not provided
        $validated['check_in_time'] = $validated['check_in_time'] ?? now();

        // Ensure proper Carbon instance
        $validated['check_in_time'] = Carbon::parse($validated['check_in_time']);

        Visitor::create($validated);

        return redirect()->route('visitors.index')
               ->with('success', 'Visitor checked in successfully');
    }

    public function show(Visitor $visitor)
    {
        // Check if student is accessing their own visitor
        if (Auth::user()->role === 'student') {
            $student = Student::where('user_id', Auth::id())->first();
            if ($visitor->student_id != $student->id) {
                abort(403);
            }
        }

        $visitor->load('student');
        return view('visitors.show', compact('visitor'));
    }

    public function update(Request $request, Visitor $visitor)
    {
        // Check if the visitor is already checked out
        if ($visitor->check_out_time) {
            return redirect()->route('visitors.index')
                   ->with('error', 'Visitor already checked out');
        }

        $validated = $request->validate([
            'check_out_time' => 'nullable|date', // Added validation
        ]);

        // Set check_out_time to current time if not provided
        $checkOutTime = $validated['check_out_time'] ?? now();

        $visitor->update([
            'check_out_time' => Carbon::parse($checkOutTime)
        ]);

        return redirect()->route('visitors.index')
               ->with('success', 'Visitor checked out successfully');
    }

    public function destroy(Visitor $visitor)
    {
        // Only allow admins to delete visitor records
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $visitor->delete();

        return redirect()->route('visitors.index')
               ->with('success', 'Visitor record deleted successfully');
    }
}