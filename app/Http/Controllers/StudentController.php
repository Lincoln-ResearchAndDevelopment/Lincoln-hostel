<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentController extends Controller
{
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
        $availableRooms = Room::where('status', 'available')
                            ->whereRaw('occupied < capacity')
                            ->get();

        return view('students.create', compact('availableRooms'));
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
            'room_id' => 'required|exists:rooms,id',
            'contact_number' => 'required|string|max:20',
            'emergency_contact' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'check_in_date' => 'required|date|after_or_equal:today',
            'expected_check_out_date' => 'required|date|after:check_in_date',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make('welcome123'),
                'role' => 'student',
            ]);

            Student::create([
                'user_id' => $user->id,
                'room_id' => $validated['room_id'],
                'admission_number' => $validated['admission_number'],
                'full_name' => $validated['full_name'],
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
            ]);

            Room::where('id', $validated['room_id'])->update([
                'occupied' => DB::raw('occupied + 1'),
                'status' => DB::raw('CASE WHEN occupied + 1 >= capacity THEN "full" ELSE status END'),
            ]);
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
        $availableRooms = Room::where(function ($query) use ($student) {
            $query->where('status', 'available')
                  ->whereRaw('occupied < capacity')
                  ->orWhere('id', $student->room_id);
        })->get();

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
