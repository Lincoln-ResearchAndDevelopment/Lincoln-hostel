<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HostelController extends Controller
{
    /**
     * Display a listing of hostels
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');

        $hostels = Hostel::query()
            ->withCount('rooms')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%')
                      ->orWhere('address', 'like', '%' . $search . '%');
                });
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->latest()
            ->paginate(12);

        $stats = [
            'total_hostels' => Hostel::count(),
            'active_hostels' => Hostel::where('status', 'active')->count(),
            'total_capacity' => Hostel::join('rooms', 'hostels.id', '=', 'rooms.hostel_id')
                                      ->sum('rooms.capacity'),
            'total_rooms' => Room::whereNotNull('hostel_id')->count(),
        ];

        return view('hostels.index', compact('hostels', 'stats'));
    }

    /**
     * Show the form for creating a new hostel
     */
    public function create()
    {
        return view('hostels.create');
    }

    /**
     * Store a newly created hostel
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:hostels,code',
            'type' => 'required|in:male,female,mixed',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('hostels', 'public');
            $validated['image_path'] = $imagePath;
        }

        Hostel::create($validated);

        return redirect()->route('hostels.index')->with('success', 'Hostel created successfully!');
    }

    /**
     * Display the specified hostel
     */
    public function show(Hostel $hostel)
    {
        $hostel->load(['rooms.students']);
        
        $stats = [
            'total_rooms' => $hostel->rooms()->count(),
            'available_rooms' => $hostel->rooms()->where('status', 'available')
                                          ->whereRaw('occupied < capacity')->count(),
            'occupied_rooms' => $hostel->rooms()->where('status', 'full')->count(),
            'maintenance_rooms' => $hostel->rooms()->where('status', 'maintenance')->count(),
            'total_capacity' => $hostel->rooms()->sum('capacity'),
            'occupied_spaces' => $hostel->students()->where('students.status', 'active')->count(),
        ];

        return view('hostels.show', compact('hostel', 'stats'));
    }

    /**
     * Show the form for editing the specified hostel
     */
    public function edit(Hostel $hostel)
    {
        return view('hostels.edit', compact('hostel'));
    }

    /**
     * Update the specified hostel
     */
    public function update(Request $request, Hostel $hostel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:hostels,code,' . $hostel->id,
            'type' => 'required|in:male,female,mixed',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($hostel->image_path) {
                \Storage::disk('public')->delete($hostel->image_path);
            }
            $imagePath = $request->file('image')->store('hostels', 'public');
            $validated['image_path'] = $imagePath;
        }

        $hostel->update($validated);

        return redirect()->route('hostels.index')->with('success', 'Hostel updated successfully!');
    }

    /**
     * Remove the specified hostel
     */
    public function destroy(Hostel $hostel)
    {
        // Check if hostel has rooms with students
        if ($hostel->students()->where('students.status', 'active')->exists()) {
            return redirect()->route('hostels.index')
                           ->with('error', 'Cannot delete hostel with active students. Please reassign students first.');
        }

        // Delete hostel image if exists
        if ($hostel->image_path) {
            \Storage::disk('public')->delete($hostel->image_path);
        }

        $hostel->delete();

        return redirect()->route('hostels.index')->with('success', 'Hostel deleted successfully!');
    }

    /**
     * Display rooms in a specific hostel
     */
    public function rooms(Hostel $hostel)
    {
        $rooms = $hostel->rooms()->withCount('students')->paginate(15);
        return view('hostels.rooms', compact('hostel', 'rooms'));
    }
}
