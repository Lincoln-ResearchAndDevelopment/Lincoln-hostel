<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Support\CampusContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.programs.index', compact('programs'));
    }

    public function store(Request $request)
    {
        // A programme always belongs to one campus. A campus admin uses their own; a
        // master super admin must first pick the campus they are working in.
        $campusId = CampusContext::currentCampusId();
        if (!$campusId) {
            return back()->withInput()->with('error', 'Choose the campus you are working in first (Super Admin > Campuses > Switch), then add the program.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('programs', 'name')->where('campus_id', $campusId)],
            'sort_order' => 'nullable|integer|min:0',
        ]);

        Program::create([
            'name' => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
            'campus_id' => $campusId,
        ]);

        return back()->with('success', 'Program created successfully.');
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('programs', 'name')->where('campus_id', $program->campus_id)->ignore($program->id)],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $program->update([
            'name' => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return back()->with('success', 'Program deleted successfully.');
    }
}
