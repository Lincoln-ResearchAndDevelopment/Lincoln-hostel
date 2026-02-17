<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Mail\HostelApplicationMail;
use App\Mail\AdminApplicationNotificationMail;
use App\Models\HostelApplication;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HostelApplicationController extends Controller
{
    public function create()
    {
        return view('apply');
    }

    public function store(Request $request)
    {
        \Log::info('Hostel application store request received', ['data' => $request->except(['passport_photo', 'applicationform_receipt', 'hostelfee_receipt', 'medical_report', 'birth_certificate', 'admission_letter'])]);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            // Academic Information
            'academic_year' => 'required|string|max:255',
            'amount_paid' => 'required|string|max:255',
            
            // Student Information
            'full_name' => 'required|string|max:255',
            'student_id' => 'required|string|max:255|unique:hostel_applications,student_id',
            'intake' => 'required|string|max:255',
            'program' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'home_address' => 'required|string|max:1000',
            'nationality' => 'required|string|max:100',
            'state_of_origin' => 'required|string|max:100',
            'local_government' => 'required|string|max:100',
            
            // Parent/Guardian Information
            'parent_full_name' => 'required|string|max:255',
            'parent_relationship' => 'required|string|max:100',
            'parent_phone' => 'required|string|max:20',
            'parent_email' => 'nullable|email|max:255',
            'parent_address' => 'required|string|max:1000',
            'parent_occupation' => 'required|string|max:255',
            'parent_workplace' => 'nullable|string|max:255',
            
            // Emergency Contact
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_address' => 'required|string|max:1000',
            
            // Medical Information
            'medical_conditions' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'medications' => 'nullable|string|max:1000',
            'blood_group' => 'nullable|string|max:10',
            'genotype' => 'nullable|string|max:10',
            'dietary_requirements' => 'nullable|string|max:1000',
            'has_disability' => 'boolean',
            'disability_details' => 'nullable|string|max:1000',
            'smoking_status' => 'required|in:non-smoker,smoker',
            'vaccination_status' => 'nullable|string|max:255',
            'insurance_info' => 'nullable|string|max:255',
            'preferred_hospital' => 'nullable|string|max:255',
            'physical_restrictions' => 'nullable|string|max:1000',
            
            // Accommodation Preferences
            'preferred_hostel_type' => 'nullable|in:male,female,mixed',
            'preferred_room_type' => 'nullable|in:single,double,triple,quad,dormitory',
            'special_accommodation_needs' => 'nullable|string|max:1000',
            
            // Documents
            'passport_photo' => 'required|image|mimes:jpg,jpeg,png|max:10240',
            'applicationform_receipt' => 'required|image|mimes:jpg,jpeg,png,pdf|max:10240',
            'hostelfee_receipt' => 'required|image|mimes:jpg,jpeg,png,pdf|max:10240',
            'medical_report' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'birth_certificate' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'admission_letter' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            
            // Declaration and Signatures
            'declaration_name' => 'required|string|max:255',
            'applicant_signature' => 'required|string|max:255',
            'applicant_date' => 'required|date',
            'guardian_signature' => 'required|string|max:255',
            'guardian_date' => 'required|date',
            
            // Additional Information
            'previous_hostel_experience' => 'nullable|string|max:1000',
            'why_choose_hostel' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            \Log::error('Hostel application validation failed', ['errors' => $validator->errors()->toArray()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Handle file uploads
        $fileFields = [
            'passport_photo',
            'applicationform_receipt', 
            'hostelfee_receipt',
            'medical_report',
            'birth_certificate',
            'admission_letter'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $this->handleFileUpload($request->file($field), $field);
            }
        }

        // Use a transaction for reliability
        DB::beginTransaction();
        try {
            $application = HostelApplication::create($validated);
            DB::commit();

            // Send emails (non-breaking if they fail)
            try {
                // Send email to student
                Mail::to($validated['email'])->send(new HostelApplicationMail($application));

                // Send notification to all admin users
                $adminUsers = User::where('is_admin', true)->where('is_active', true)->get();
                foreach ($adminUsers as $admin) {
                    Mail::to($admin->email)->send(new AdminApplicationNotificationMail($application));
                }

                // Dashboard Notification to Admins
                \App\Models\Notification::notifyAllAdmins(
                    'application',
                    'New Hostel Application',
                    'New application received from ' . $application->full_name,
                    ['application_id' => $application->id]
                );

            } catch (Exception $mailEx) {
                // Just log mail errors, don't fail the application
                \Log::warning('Hostel application emails failed: ' . $mailEx->getMessage());
            }

            return redirect()->back()->with('success', 
                'Application submitted successfully! Your application number is: ' . $application->application_number . 
                '. We will get back to you shortly via email or phone.'
            );

        } catch (Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded files if database save fails
            foreach ($fileFields as $field) {
                if (isset($validated[$field]) && $validated[$field]) {
                    $this->deleteFile($validated[$field]);
                }
            }

            \Log::error('Hostel application submission failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 
                'Something went wrong while submitting your application: ' . $e->getMessage()
            )->withInput();
        }
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($file, $fieldName)
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('hostel_applications/' . $fieldName, $filename, 'public');
        
        return 'storage/' . $path;
    }

    /**
     * Delete uploaded file
     */
    private function deleteFile($filePath)
    {
        if (!$filePath) return;

        // Try Storage first (for new files)
        $storagePath = str_replace('storage/', '', $filePath);
        if (Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
            return;
        }

        // Try public_path (for old files)
        if (file_exists(public_path($filePath))) {
            @unlink(public_path($filePath));
        }
    }

    /**
     * Show application details (for students to view their application)
     */
    public function show($applicationNumber)
    {
        $application = HostelApplication::where('application_number', $applicationNumber)->firstOrFail();
        return view('application-details', compact('application'));
    }

    /**
     * Admin view for managing applications
     */
    public function adminIndex(Request $request)
    {
        $query = HostelApplication::with('reviewer');
        
        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $applications = $query->orderBy('created_at', 'desc')->paginate(20);
            
        return view('admin.applications.index', compact('applications'));
    }

    /**
     * Admin view for single application
     */
    public function adminShow(HostelApplication $application)
    {
        return view('admin.applications.show', compact('application'));
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, HostelApplication $application)
    {
        $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Send status update email to student
        try {
            Mail::to($application->email)->send(new \App\Mail\ApplicationStatusUpdateMail($application));
        } catch (Exception $e) {
            // Log the error but don't fail the status update
            \Log::error('Failed to send status update email: ' . $e->getMessage());
        }

        // Dashboard Notification if user_id exists (meaning they are a registered student, though applications might come from guests)
        /* 
           Note: HostelApplication model usually links to a user/student if they are registered.
           If the application is from a guest (new student), we might not have a student_id yet to notify via dashboard.
           However, let's assume we notify if we can link it.
           Based on migration, there isn't a direct student_id link always, but maybe email matches?
           Let's skip dashboard notification for guest applications for now as they can't login to see it.
           But if they are converted to students, they might see it.
           Let's check if the system converts them.
           For now, I will assume dashboard notifications are only relevant for logged-in users.
        */

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Application status updated successfully.']);
        }

        return redirect()->back()->with('success', 'Application status updated successfully.');
    }
}
