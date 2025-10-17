<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $rooms = Room::withCount('students')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('room_number', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('capacity', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('gender_type', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms',
            'capacity'    => 'required|integer|min:1',
            'gender_type' => 'required|in:male,female',
            'status'      => 'required|in:available,full,maintenance',
            'description' => 'nullable|string',
        ]);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'Room created successfully');
    }

    public function show(Room $room)
    {
        $room->load('students');
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
            'capacity'    => 'required|integer|min:1',
            'gender_type' => 'required|in:male,female',
            'status'      => 'required|in:available,full,maintenance',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully');
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
