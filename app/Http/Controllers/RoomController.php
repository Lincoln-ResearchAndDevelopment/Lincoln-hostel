<?php

namespace App\Http\Controllers;

use App\Support\CampusContext;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $hostelId = $request->get('hostel_id');

        $rooms = Room::with('hostel')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('room_number', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('room_type', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($hostelId, function ($query) use ($hostelId) {
                $query->where('hostel_id', $hostelId);
            })
            ->latest()
            ->paginate(15);

        $hostels = \App\Models\Hostel::active()->get();

        return view('rooms.index', compact('rooms', 'hostels'));
    }

    public function create()
    {
        $hostels = \App\Models\Hostel::active()->get();
        return view('rooms.create', compact('hostels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostel_id' => ['required', CampusContext::exists('hostels')],
            // Room numbers only need to be unique within a hostel (every campus has a Room 101).
            'room_number' => ['required', 'string', Rule::unique('rooms', 'room_number')->where('hostel_id', $request->input('hostel_id'))],
            'room_type' => 'required|in:single,double,triple,quad,dormitory',
            'capacity' => 'required|integer|min:1',
            'price_per_semester' => 'required|numeric|min:0',
            'price_per_year' => 'required|numeric|min:0',
            'floor_number' => 'required|integer|min:1',
            'gender_type' => 'required|in:male,female',
            'status' => 'required|in:available,full,maintenance',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array',
        ]);

        // Handle facilities array
        if ($request->has('facilities')) {
            $validated['facilities'] = array_filter($request->facilities);
        }

        $room = Room::create($validated);

        // Generate associated beds based on capacity
        for ($i = 1; $i <= $room->capacity; $i++) {
            \App\Models\Bed::create([
                'room_id' => $room->id,
                'bed_number' => 'Bed ' . $i,
                'is_occupied' => false,
            ]);
        }

        return redirect()->route('rooms.index')->with('success', 'Room created successfully!');
    }

    public function show(Room $room)
    {
        $room->load('students');
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $hostels = \App\Models\Hostel::active()->get();
        return view('rooms.edit', compact('room', 'hostels'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'hostel_id' => ['required', CampusContext::exists('hostels')],
            'room_number' => ['required', 'string', Rule::unique('rooms', 'room_number')->where('hostel_id', $request->input('hostel_id'))->ignore($room->id)],
            'room_type' => 'required|in:single,double,triple,quad,dormitory',
            'capacity' => 'required|integer|min:1',
            'price_per_semester' => 'required|numeric|min:0',
            'price_per_year' => 'required|numeric|min:0',
            'floor_number' => 'required|integer|min:1',
            'gender_type' => 'required|in:male,female',
            'status' => 'required|in:available,full,maintenance',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array',
        ]);

        // Handle facilities array
        if ($request->has('facilities')) {
            $validated['facilities'] = array_filter($request->facilities);
        }

        $room->update($validated);

        $currentBedsCount = \App\Models\Bed::where('room_id', $room->id)->count();
        
        if ($room->capacity > $currentBedsCount) {
            // Add missing beds
            for ($i = $currentBedsCount + 1; $i <= $room->capacity; $i++) {
                \App\Models\Bed::create([
                    'room_id' => $room->id,
                    'bed_number' => 'Bed ' . $i,
                    'is_occupied' => false,
                ]);
            }
        } elseif ($room->capacity < $currentBedsCount) {
            // Delete extra unoccupied beds
            $extraBeds = \App\Models\Bed::where('room_id', $room->id)
                ->where('is_occupied', false)
                ->orderBy('id', 'desc')
                ->limit($currentBedsCount - $room->capacity)
                ->get();
                
            foreach ($extraBeds as $bed) {
                $bed->delete();
            }
        }

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully!');
    }

    public function destroy(Room $room)
    {
        if ($room->students()->exists()) {
            return redirect()->route('rooms.index')->with('error', 'Room cannot be deleted as it has students assigned');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully');
    }
}
