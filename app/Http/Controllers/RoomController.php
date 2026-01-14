<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $hostelId = $request->get('hostel_id');

        $rooms = Room::with('hostel')
            ->withCount('students')
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
            'hostel_id' => 'required|exists:hostels,id',
            'room_number' => 'required|string|unique:rooms',
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

        Room::create($validated);

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
            'hostel_id' => 'required|exists:hostels,id',
            'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
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
