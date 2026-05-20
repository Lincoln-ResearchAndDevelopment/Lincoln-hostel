<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BedController extends Controller
{
    /**
     * Display beds for a specific room
     */
    public function index(Room $room)
    {
        $beds = Bed::where('room_id', $room->id)
            ->with('student')
            ->orderBy('bed_number')
            ->get();

        return view('beds.index', compact('room', 'beds'));
    }

    /**
     * Store a new bed for a room
     */
    public function store(Request $request, Room $room)
    {
        $validated = $request->validate([
            'bed_number' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($room) {
                    $exists = Bed::where('room_id', $room->id)
                        ->where('bed_number', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail("Bed number '{$value}' already exists in this room.");
                    }
                },
            ],
        ]);

        // Check if adding this bed would exceed room capacity
        $currentBedCount = Bed::where('room_id', $room->id)->count();
        
        if ($currentBedCount >= $room->capacity) {
            return back()->withErrors([
                'bed_number' => "Cannot add more beds. Room capacity is {$room->capacity}. Consider increasing room capacity first."
            ])->withInput();
        }

        Bed::create([
            'room_id' => $room->id,
            'bed_number' => $validated['bed_number'],
            'is_occupied' => false,
        ]);

        return redirect()->route('beds.index', $room)
            ->with('success', "Bed '{$validated['bed_number']}' created successfully.");
    }

    /**
     * Update bed number
     */
    public function update(Request $request, Room $room, Bed $bed)
    {
        // Ensure bed belongs to this room
        if ($bed->room_id != $room->id) {
            abort(404);
        }

        $validated = $request->validate([
            'bed_number' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($room, $bed) {
                    $exists = Bed::where('room_id', $room->id)
                        ->where('bed_number', $value)
                        ->where('id', '!=', $bed->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail("Bed number '{$value}' already exists in this room.");
                    }
                },
            ],
        ]);

        $bed->update(['bed_number' => $validated['bed_number']]);

        return redirect()->route('beds.index', $room)
            ->with('success', 'Bed number updated successfully.');
    }

    /**
     * Delete a bed (only if unoccupied)
     */
    public function destroy(Room $room, Bed $bed)
    {
        // Ensure bed belongs to this room
        if ($bed->room_id != $room->id) {
            abort(404);
        }

        if ($bed->is_occupied) {
            return back()->withErrors([
                'error' => "Cannot delete bed '{$bed->bed_number}' because it is currently occupied by {$bed->student->full_name}."
            ]);
        }

        $bedNumber = $bed->bed_number;
        $bed->delete();

        return redirect()->route('beds.index', $room)
            ->with('success', "Bed '{$bedNumber}' deleted successfully.");
    }
}
