<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $user = Auth::user();

        // Mark complaints as read for admin users
        if ($user->role !== 'student') {
            Complaint::where('is_read', false)->update(['is_read' => true]);
        }

        // Build query based on role
        $complaintsQuery = Complaint::with('student');

        if ($user->role === 'student') {
            $student = Student::where('user_id', $user->id)->firstOrFail();
            $complaintsQuery->where('student_id', $student->id);
        }

        // Apply search if provided
        if ($search) {
            $complaintsQuery->where(function ($query) use ($search) {
                $query->where('subject', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($subQuery) use ($search) {
                        $subQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        // Order and paginate
        $complaints = $complaintsQuery->latest()->paginate(10);

        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'student') {
            $student = Student::where('user_id', $user->id)->firstOrFail();
            return view('complaints.create', compact('student'));
        }

        $students = Student::where('status', 'active')->get();
        return view('complaints.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string',
        ]);

        $validated['status'] = 'submitted';

        Complaint::create($validated);

        return redirect()->route('complaints.index')->with('success', 'Complaint submitted successfully');
    }

    public function show(Complaint $complaint)
    {
        $user = Auth::user();

        if ($user->role === 'student') {
            $student = Student::where('user_id', $user->id)->firstOrFail();
            if ($complaint->student_id !== $student->id) {
                abort(403);
            }
        }

        $complaint->load('student');
        return view('complaints.show', compact('complaint'));
    }

    public function edit(Complaint $complaint)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $students = Student::where('status', 'active')->get();
        return view('complaints.edit', compact('complaint', 'students'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string',
            'status'       => 'required|in:submitted,in progress,resolved',
            'resolution'   => 'nullable|string|required_if:status,resolved',
        ]);

        if ($validated['status'] === 'resolved' && $complaint->status !== 'resolved') {
            $validated['resolved_at'] = now();
        }

        $complaint->update($validated);

        return redirect()->route('complaints.index')->with('success', 'Complaint updated successfully');
    }

    public function destroy(Complaint $complaint)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $complaint->delete();

        return redirect()->route('complaints.index')->with('success', 'Complaint deleted successfully');
    }
}
