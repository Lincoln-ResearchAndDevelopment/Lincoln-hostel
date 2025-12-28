<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    public function index()
    {
        $student = auth('student')->user();
        return view('student.profile.index', compact('student'));
    }

    public function edit()
    {
        $student = auth('student')->user();
        return view('student.profile.edit', compact('student'));
    }

    public function update(Request $request)
    {
        $student = auth('student')->user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        $student->update($request->only(['full_name', 'contact_number', 'address', 'department']));

        // Also sync user name if linked
        if ($student->user) {
            $student->user->update(['name' => $student->full_name]);
        }

        return redirect('/student/profile')->with('success', 'Profile updated successfully!');
    }

    public function changePasswordForm()
    {
        return view('student.profile.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $student = auth('student')->user();

        if (!Hash::check($request->current_password, $student->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        $student->update(['password' => Hash::make($request->new_password)]);

        return redirect('/student/profile')->with('success', 'Password changed successfully.');
    }
}
