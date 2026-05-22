<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Mail\HostelApplicationMail;
use App\Mail\AdminApplicationNotificationMail;
use App\Models\HostelApplication;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Bed;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Traits\TracksEmails;

use App\Models\Department;
use App\Models\Intake;

class HostelApplicationController extends Controller
{
    use TracksEmails;
    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $intakes = Intake::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        return view('apply', compact('departments', 'intakes'));
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

            // Send emails with comprehensive tracking
            try {
                // Send confirmation email to student
                $studentEmailResult = $this->sendTrackedEmail(
                    'application_received',
                    $validated['email'],
                    new \App\Mail\ApplicationReceivedMail($application),
                    [
                        'application_id' => $application->id,
                        'student_name' => $application->full_name,
                        'operation' => 'student_application_confirmation'
                    ]
                );
                
                $this->logEmailResult($studentEmailResult, 'Student Application Confirmation');

                // Send notification to all admin users
                $adminUsers = User::where('is_admin', true)->where('is_active', true)->get();
                $adminEmails = $adminUsers->pluck('email')->toArray();
                
                if (!empty($adminEmails)) {
                    $adminEmailResults = $this->sendBatchEmails(
                        'admin_notification',
                        $adminEmails,
                        new AdminApplicationNotificationMail($application),
                        [
                            'application_id' => $application->id,
                            'student_name' => $application->full_name,
                            'operation' => 'admin_application_notification'
                        ]
                    );
                    
                    Log::info("📧 ADMIN NOTIFICATION BATCH", [
                        'batch_id' => $adminEmailResults['batch_id'],
                        'total_admins' => $adminEmailResults['total'],
                        'successful_notifications' => $adminEmailResults['success'],
                        'failed_notifications' => $adminEmailResults['failures']
                    ]);
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
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        
        $applications = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
            
        return view('admin.applications.index', compact('applications'));
    }

    /**
     * Admin view for single application
     */
    public function adminShow(HostelApplication $application)
    {
        $hostels = Hostel::active()->get();
        return view('admin.applications.show', compact('application', 'hostels'));
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

        // Send status update email to student with comprehensive tracking
        try {
            if ($application->status === 'approved') {
                $emailResult = $this->sendTrackedEmail(
                    'application_approved',
                    $application->email,
                    new \App\Mail\ApplicationApprovedMail($application),
                    [
                        'application_id' => $application->id,
                        'student_name' => $application->full_name,
                        'operation' => 'application_approval_notification'
                    ]
                );
                $this->logEmailResult($emailResult, 'Application Approval Notification');
                
            } elseif ($application->status === 'rejected') {
                $emailResult = $this->sendTrackedEmail(
                    'application_rejected',
                    $application->email,
                    new \App\Mail\ApplicationRejectedMail($application),
                    [
                        'application_id' => $application->id,
                        'student_name' => $application->full_name,
                        'operation' => 'application_rejection_notification'
                    ]
                );
                $this->logEmailResult($emailResult, 'Application Rejection Notification');
                
            } else {
                // For 'under_review' or others, use a generic status update if needed
                $emailResult = $this->sendTrackedEmail(
                    'application_received', // Using closest match for generic updates
                    $application->email,
                    new \App\Mail\ApplicationStatusUpdateMail($application),
                    [
                        'application_id' => $application->id,
                        'student_name' => $application->full_name,
                        'new_status' => $application->status,
                        'operation' => 'application_status_update'
                    ]
                );
                $this->logEmailResult($emailResult, 'Application Status Update');
            }
        } catch (Exception $e) {
            // Log the error but don't fail the status update
            Log::error('❌ APPLICATION STATUS EMAIL FAILED', [
                'application_id' => $application->id,
                'status' => $application->status,
                'email' => $application->email,
                'error' => $e->getMessage()
            ]);
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

    /**
     * Get available rooms for a hostel.
     */
    public function getAvailableRooms(Hostel $hostel)
    {
        $rooms = Room::where('hostel_id', $hostel->id)
            ->where('status', 'available')
            ->whereRaw('occupied < capacity')
            ->get(['id', 'room_number', 'occupied', 'capacity', 'room_type']);

        return response()->json($rooms);
    }

    /**
     * Get available beds for a room.
     */
    public function getAvailableBeds(Room $room)
    {
        $beds = Bed::where('room_id', $room->id)
            ->where('is_occupied', false)
            ->get(['id', 'bed_number', 'is_occupied']);

        return response()->json($beds);
    }

    /**
     * Approve and assign room/bed to student.
     */
    public function approveAndAssign(Request $request, HostelApplication $application)
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'room_id' => 'required|exists:rooms,id',
            'bed_id' => 'required|exists:beds,id',
            'admin_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $room = Room::findOrFail($request->room_id);
            $bed = Bed::findOrFail($request->bed_id);

            if ($room->occupied >= $room->capacity) {
                return back()->with('error', 'Room is already full.');
            }
            if ($bed->is_occupied) {
                return back()->with('error', 'Bed is already occupied.');
            }

            // 1. Find or Create Student & User
            $student = Student::where('admission_number', $application->student_id)->first();
            
            if (!$student) {
                // Check if user exists with this email
                $user = User::where('email', $application->email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $application->full_name,
                        'email' => $application->email,
                        'password' => Hash::make('welcome123'),
                        'role' => 'student',
                    ]);
                }

                $student = Student::create([
                    'user_id' => $user->id,
                    'admission_number' => $application->student_id,
                    'full_name' => $application->full_name,
                    'email' => $application->email,
                    'gender' => $application->gender,
                    'date_of_birth' => $application->date_of_birth,
                    'nationality' => $application->nationality,
                    'state_of_origin' => $application->state_of_origin,
                    'local_government' => $application->local_government,
                    'department' => $application->department,
                    'intake' => $application->intake,
                    'semester' => 1,
                    'contact_number' => $application->phone_number,
                    'emergency_contact' => $application->emergency_contact_phone,
                    'address' => $application->home_address,
                    'parent_name' => $application->parent_full_name,
                    'parent_relationship' => $application->parent_relationship,
                    'parent_phone' => $application->parent_phone,
                    'parent_email' => $application->parent_email,
                    'parent_address' => $application->parent_address,
                    'parent_occupation' => $application->parent_occupation,
                    'application_id' => $application->id,
                    'status' => 'active',
                ]);
            } else {
                // Update existing student with latest application info
                $student->update([
                    'application_id' => $application->id,
                    'department' => $application->department,
                    'intake' => $application->intake,
                ]);
            }

            // 2. Atomic Increment Room
            Room::where('id', $room->id)->update([
                'occupied' => DB::raw('occupied + 1'),
                'status' => DB::raw('CASE WHEN occupied + 1 >= capacity THEN "full" ELSE "available" END'),
            ]);

            // 3. Update Bed status
            $bed->update([
                'is_occupied' => true,
                'student_id' => $student->id
            ]);

            // 4. Update Student Record with room/bed and fees
            $student->update([
                'room_id' => $room->id,
                'bed_id' => $bed->id,
                'hostel_fee_amount' => $application->amount_paid,
                'hostel_fee_paid' => $application->amount_paid,
                'hostel_fee_status' => 'paid',
                'check_in_date' => now(),
                'expected_check_out_date' => now()->addMonths(4), // Default 1 semester
            ]);

            // 5. Finalize Application
            $application->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            DB::commit();

            // Send notification email to student
            try {
                $this->sendTrackedEmail('application_approved', $application->email, new \App\Mail\ApplicationApprovedMail($application), ['application_id' => $application->id]);
                $this->sendTrackedEmail('room_assigned', $application->email, new \App\Mail\RoomAssignedMail($student, $room), ['student_id' => $student->id, 'room_id' => $room->id]);
            } catch (\Exception $e) {
                \Log::error('Status Update Email Failed: ' . $e->getMessage());
                // Non-blocking error, we still redirect with success but mention the email failure if needed
            }

            return redirect()->route('applications.show', $application)->with('success', 'Application approved and room assigned successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Allocation Failed: ' . $e->getMessage());
            return back()->with('error', 'Allocation Failed: ' . $e->getMessage());
        }
    }

    /**
     * Reject application with reason.
     */
    public function reject(Request $request, HostelApplication $application)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Send notification email
        try {
            $this->sendTrackedEmail('application_rejected', $application->email, new \App\Mail\ApplicationRejectedMail($application), ['application_id' => $application->id]);
        } catch (\Exception $e) {
            \Log::error('Rejection Email Failed: ' . $e->getMessage());
        }

        return redirect()->route('applications.index')->with('success', 'Application rejected.');
    }
}
