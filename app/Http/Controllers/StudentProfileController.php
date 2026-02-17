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

        // Auto-sync missing data from application if available
        if ($student->application_id && $student->hostelApplication) {
            $application = $student->hostelApplication;
            
            // Only update if critical fields are missing to avoid overwriting user edits
            if (empty($student->parent_name) || empty($student->date_of_birth)) {
                $student->update([
                    'date_of_birth' => $student->date_of_birth ?? $application->date_of_birth,
                    'nationality' => $student->nationality ?? $application->nationality,
                    'state_of_origin' => $student->state_of_origin ?? $application->state_of_origin,
                    'local_government' => $student->local_government ?? $application->local_government,
                    
                    'parent_name' => $student->parent_name ?? $application->parent_full_name,
                    'parent_relationship' => $student->parent_relationship ?? $application->parent_relationship,
                    'parent_phone' => $student->parent_phone ?? $application->parent_phone,
                    'parent_email' => $student->parent_email ?? $application->parent_email,
                    'parent_address' => $student->parent_address ?? $application->parent_address,
                    'parent_occupation' => $student->parent_occupation ?? $application->parent_occupation,
                    
                    'blood_group' => $student->blood_group ?? $application->blood_group,
                    'genotype' => $student->genotype ?? $application->genotype,
                    'medical_conditions' => $student->medical_conditions ?? $application->medical_conditions,
                    'allergies' => $student->allergies ?? $application->allergies,
                    'medications' => $student->medications ?? $application->medications,
                    'has_disability' => $student->has_disability ? $student->has_disability : ($application->has_disability ?? false),
                    'disability_details' => $student->disability_details ?? $application->disability_details,
                ]);
            }
        }

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
