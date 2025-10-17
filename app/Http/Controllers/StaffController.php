<?php
// app/Http/Controllers/StaffController.php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::all();
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'staff_gender' => 'required|in:male,female',
            'assigned_hostel_gender' => 'required|in:male,female'
        ]);

        Staff::create($request->all());

        return redirect()->route('staff.index')->with('success', 'Staff created successfully.');
    }

    public function edit(Staff $staff)
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'staff_gender' => 'required|in:male,female',
            'assigned_hostel_gender' => 'required|in:male,female'
        ]);

        $staff->update($request->all());

        return redirect()->route('staff.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff deleted successfully.');
    }
    
}