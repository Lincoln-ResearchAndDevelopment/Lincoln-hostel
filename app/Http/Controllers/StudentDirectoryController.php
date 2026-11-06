<?php

namespace App\Http\Controllers;

use App\Models\Student;

class StudentDirectoryController extends Controller
{
    public function index()
    {
        $student = auth()->guard('student')->user();

        if (!$student->room_id) {
            $students = collect();
            return view('student.directory', compact('student', 'students'));
        }

        $students = Student::where('room_id', $student->room_id)
            ->select('id', 'full_name', 'admission_number', 'program', 'department', 'semester', 'intake')
            ->orderBy('full_name')
            ->get();

        return view('student.directory', compact('student', 'students'));
    }
}
