<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Room;
use App\Models\User;
use App\Models\HostelApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Search for approved applications
     */
    public function searchApplications(Request $request)
    {
        $search = $request->get('q');
        
        $applications = HostelApplication::where('status', 'approved')
            ->where(function($query) use ($search) {
                $query->where('full_name', 'like', "%$search%")
                      ->orWhere('student_id', 'like', "%$search%");
            })
            ->limit(10)
            ->get();

        return response()->json($applications);
    }

    public function index()
    {
        $search = request('search');

        $students = Student::with('room')
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', '%'.$search.'%')
                      ->orWhere('admission_number', 'like', '%'.$search.'%')
                      ->orWhere('department', 'like', '%'.$search.'%')
                      ->orWhere('gender', 'like', '%'.$search.'%')
                      ->orWhere('contact_number', 'like', '%'.$search.'%')
                      ->orWhereHas('room', function($roomQuery) use ($search) {
                          $roomQuery->where('room_number', 'like', '%'.$search.'%');
                      });
                });
            })
            ->latest()
            ->paginate(10);

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_number' => 'required|string|unique:students|max:50',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'gender' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:20',
            'intake' => 'required|in:March 2023,July 2023,November 2023,March 2024,July 2024,November 2024,March 2025',
            // Room assignment removed - students must book through the portal
            'contact_number' => 'required|string|max:20',
            'emergency_contact' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'check_in_date' => 'required|date|after_or_equal:today',
            'expected_check_out_date' => 'required|date|after:check_in_date',
        ]);


        // Check if there is an approved application (optional for manual entry)
        $application = HostelApplication::where('student_id', $validated['admission_number'])
            ->where('status', 'approved')
            ->first();

        DB::transaction(function () use ($validated, $application) {
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make('welcome123'),
                'role' => 'student',
            ]);

            Student::create([
                'user_id' => $user->id,
                'application_id' => $application ? $application->id : null,
                'room_id' => null, // Students must book rooms through the booking portal
                'admission_number' => $validated['admission_number'],
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'gender' => $validated['gender'],
                'department' => $validated['department'],
                'semester' => $validated['semester'],
                'intake' => $validated['intake'],
                'contact_number' => $validated['contact_number'],
                'emergency_contact' => $validated['emergency_contact'],
                'address' => $validated['address'],
                'check_in_date' => Carbon::parse($validated['check_in_date']),
                'expected_check_out_date' => Carbon::parse($validated['expected_check_out_date']),
                'status' => 'active',
                'hostel_fee_amount' => optional($application)->amount_paid ?? 0,
                'hostel_fee_paid' => optional($application)->amount_paid ?? 0,
                'hostel_fee_status' => 'paid',

                // Transfer additional data from Application if available
                'date_of_birth' => optional($application)->date_of_birth,
                'nationality' => optional($application)->nationality,
                'state_of_origin' => optional($application)->state_of_origin,
                'local_government' => optional($application)->local_government,
                
                // Parent/Guardian Info
                'parent_name' => optional($application)->parent_full_name,
                'parent_relationship' => optional($application)->parent_relationship,
                'parent_phone' => optional($application)->parent_phone,
                'parent_email' => optional($application)->parent_email,
                'parent_address' => optional($application)->parent_address,
                'parent_occupation' => optional($application)->parent_occupation,
                
                // Medical Info
                'blood_group' => optional($application)->blood_group,
                'genotype' => optional($application)->genotype,
                'medical_conditions' => optional($application)->medical_conditions,
                'allergies' => optional($application)->allergies,
                'medications' => optional($application)->medications,
                'has_disability' => optional($application)->has_disability ?? false,
                'disability_details' => optional($application)->disability_details,
            ]);

            // No room assignment during registration
            // Students book rooms through the student portal
        });

        return redirect()->route('students.index')->with('success', 'Student registered successfully');
    }

    public function show(Student $student)
    {
        $student->load(['room', 'payments', 'complaints', 'visitors']);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $availableRooms = Room::with('hostel')
            ->where(function ($query) use ($student) {
                $query->where('status', 'available')
                      ->whereRaw('occupied < capacity')
                      ->orWhere('id', $student->room_id);
            })
            ->get()
            ->sortBy(function($room) {
                return $room->hostel->name . ' ' . $room->room_number;
            });

        return view('students.edit', compact('student', 'availableRooms'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'admission_number' => 'required|string|max:50|unique:students,admission_number,' . $student->id,
            'full_name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:20',
            'intake' => 'required|in:March 2023,July 2023,November 2023,March 2024,July 2024,November 2024,March 2025',
            'room_id' => 'required|exists:rooms,id',
            'contact_number' => 'required|string|max:20',
            'emergency_contact' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'check_in_date' => 'required|date',
            'expected_check_out_date' => 'required|date|after:check_in_date',
        ]);

        DB::transaction(function () use ($validated, $student) {
            if ($student->room_id != $validated['room_id']) {
                Room::where('id', $student->room_id)->update([
                    'occupied' => DB::raw('occupied - 1'),
                    'status' => DB::raw('CASE WHEN occupied - 1 < capacity THEN "available" ELSE status END'),
                ]);

                Room::where('id', $validated['room_id'])->update([
                    'occupied' => DB::raw('occupied + 1'),
                    'status' => DB::raw('CASE WHEN occupied + 1 >= capacity THEN "full" ELSE status END'),
                ]);
            }

            $student->update([
                'admission_number' => $validated['admission_number'],
                'full_name' => $validated['full_name'],
                'gender' => $validated['gender'],
                'department' => $validated['department'],
                'semester' => $validated['semester'],
                'intake' => $validated['intake'],
                'room_id' => $validated['room_id'],
                'contact_number' => $validated['contact_number'],
                'emergency_contact' => $validated['emergency_contact'],
                'address' => $validated['address'],
                'check_in_date' => Carbon::parse($validated['check_in_date']),
                'expected_check_out_date' => Carbon::parse($validated['expected_check_out_date']),
            ]);

            $student->user->update([
                'name' => $validated['full_name'],
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Student details updated successfully.');
    }

    public function destroy(Student $student)
    {
        DB::transaction(function () use ($student) {
            $roomId = $student->room_id;
            $userId = $student->user_id;

            if ($student->status === 'active' && $roomId) {
                Room::where('id', $roomId)->update([
                    'occupied' => DB::raw('occupied - 1'),
                    'status' => DB::raw('CASE WHEN occupied - 1 < capacity AND status = "full" THEN "available" ELSE status END'),
                ]);
            }

            $student->delete();
            User::destroy($userId);
        });

        return redirect()->route('students.index')->with('success', 'Student deleted successfully');
    }
}
