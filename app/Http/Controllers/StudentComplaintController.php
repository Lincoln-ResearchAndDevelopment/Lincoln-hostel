<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class StudentComplaintController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $student = auth('student')->user();

        $complaint = Complaint::create([
            'student_id' => $student->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'submitted',
        ]);

        return redirect()->back()->with('complaint_success', 'Complaint submitted successfully!');
    }
}