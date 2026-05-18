<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Room;
use App\Models\User;
use App\Models\HostelApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Department;
use App\Models\Intake;
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
        $departments = Department::where('is_active', true)->orderBy('sort_order')->get();
        $intakes = Intake::where('is_active', true)->orderBy('sort_order')->get();
        return view('students.create', compact('departments', 'intakes'));
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
            'intake' => 'required|string|max:100', // Flexible intake
            'contact_number' => 'required|string|max:20',
            'emergency_contact' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'check_in_date' => 'required|date|after_or_equal:today',
            'expected_check_out_date' => 'required|date|after:check_in_date',

            // Demographic Info
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'state_of_origin' => 'nullable|string|max:100',
            'local_government' => 'nullable|string|max:100',
            
            // Parent/Guardian Info
            'parent_name' => 'nullable|string|max:255',
            'parent_relationship' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'nullable|email|max:255',
            'parent_address' => 'nullable|string|max:255',
            'parent_occupation' => 'nullable|string|max:100',
            
            // Medical Info
            'blood_group' => 'nullable|string|max:10',
            'genotype' => 'nullable|string|max:10',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'has_disability' => 'nullable|boolean',
            'disability_details' => 'nullable|string',
        ]);


        // Check if there is an approved application (optional for manual entry)
        $application = HostelApplication::where('student_id', $validated['admission_number'])
            ->where('status', 'approved')
            ->first();

        DB::transaction(function () use ($validated, $application, $request) {
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make('welcome123'),
                'role' => 'student',
            ]);

            Student::create(array_merge($validated, [
                'user_id' => $user->id,
                'application_id' => $application ? $application->id : null,
                'room_id' => null, // Students must book rooms through the booking portal
                'status' => 'active',
                'hostel_fee_amount' => $validated['hostel_fee_amount'] ?? optional($application)->amount_paid ?? 0,
                'hostel_fee_paid' => $validated['hostel_fee_paid'] ?? optional($application)->amount_paid ?? 0,
                'hostel_fee_status' => 'paid',
                'has_disability' => $request->has('has_disability'),
            ]));

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

        $departments = Department::where('is_active', true)->orderBy('sort_order')->get();
        $intakes = Intake::where('is_active', true)->orderBy('sort_order')->get();

        return view('students.edit', compact('student', 'availableRooms', 'departments', 'intakes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'admission_number' => 'required|string|max:50|unique:students,admission_number,' . $student->id,
            'full_name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:20',
            'intake' => 'required|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'contact_number' => 'required|string|max:20',
            'emergency_contact' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'check_in_date' => 'required|date',
            'expected_check_out_date' => 'required|date|after:check_in_date',

            // Demographic Info
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'state_of_origin' => 'nullable|string|max:100',
            'local_government' => 'nullable|string|max:100',
            
            // Parent/Guardian Info
            'parent_name' => 'nullable|string|max:255',
            'parent_relationship' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'nullable|email|max:255',
            'parent_address' => 'nullable|string|max:255',
            'parent_occupation' => 'nullable|string|max:100',
            
            // Medical Info
            'blood_group' => 'nullable|string|max:10',
            'genotype' => 'nullable|string|max:10',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'has_disability' => 'nullable|boolean',
            'disability_details' => 'nullable|string',
        ]);

        $oldRoomId = $student->room_id;
        $newRoomId = $validated['room_id'] ?? null;

        DB::transaction(function () use ($validated, $student, $request, $oldRoomId, $newRoomId) {
            if ($oldRoomId != $newRoomId) {
                // Atomic Decrement Old Room
                if ($oldRoomId) {
                    Room::where('id', $oldRoomId)->where('occupied', '>', 0)->update([
                        'occupied' => DB::raw('occupied - 1'),
                        'status' => DB::raw('CASE WHEN occupied - 1 < capacity THEN "available" ELSE status END'),
                    ]);
                }

                // Atomic Increment New Room
                if ($newRoomId) {
                    Room::where('id', $newRoomId)->update([
                        'occupied' => DB::raw('occupied + 1'),
                        'status' => DB::raw('CASE WHEN occupied + 1 >= capacity THEN "full" ELSE status END'),
                    ]);
                }
            }

            $validated['has_disability'] = $request->has('has_disability');
            $student->update($validated);

            if ($student->user) {
                $student->user->update([
                    'name' => $validated['full_name'],
                ]);
            }
        });

        // Trigger email if room changed and new room is assigned
        if ($oldRoomId != $newRoomId && $newRoomId) {
            try {
                $roomObj = Room::find($newRoomId);
                if ($roomObj) {
                    Mail::to($student->email)->send(new \App\Mail\RoomAssignedMail($student, $roomObj));
                }
            } catch (\Exception $e) {
                \Log::error('Manual Room Assignment Email Failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('students.index')->with('success', 'Student details updated successfully.');
    }

    public function destroy(Student $student)
    {
        DB::transaction(function () use ($student) {
            $roomId = $student->room_id;
            $userId = $student->user_id;

            if ($student->status === 'active' && $roomId) {
                Room::where('id', $roomId)->where('occupied', '>', 0)->update([
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






