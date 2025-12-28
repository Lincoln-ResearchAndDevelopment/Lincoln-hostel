<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\SystemAlert;
use Illuminate\Http\Request;

class StudentComplaintController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // max 5MB
        ]);

        $student = auth('student')->user();

        // Create complaint
        $complaint = Complaint::create([
            'student_id' => $student->id,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'submitted',
        ]);

        // Handle optional attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = 'complaint_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('complaints', $fileName, 'public');

            $complaint->attachment_path = $filePath;
            $complaint->save();
        }

        // Create a system alert for admins so they can see new complaints immediately
        SystemAlert::create([
            'title' => 'New complaint from ' . $student->full_name,
            'message' => 'New complaint submitted: ' . $validated['subject'] . "\nView: " . url('/complaints/' . $complaint->id),
            'is_read' => false,
        ]);

        // When redirecting, open the complaint history panel so the student can see their submission
        return redirect()->back()->with('complaint_success', 'Complaint submitted successfully!')
                     ->with('complaint_panel', 'complaint-history');

    }
}