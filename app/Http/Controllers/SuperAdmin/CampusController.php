<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Campus management for the master super admin: add a campus, set its payment
 * account, switch it on or off. Each campus then gets its own admins, hostels,
 * rooms and applications.
 */
class CampusController extends Controller
{
    private function ensureMaster()
    {
        $superAdmin = Auth::guard('superadmin')->user();
        if (!$superAdmin || !$superAdmin->is_master) {
            return redirect()->route('superadmin.dashboard')
                ->with('error', 'Only the Master Super Admin can manage campuses.');
        }

        return null;
    }

    private function rules(?Campus $campus = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:10', 'alpha_dash', Rule::unique('campuses', 'code')->ignore($campus?->id)],
            'address' => 'nullable|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            // Service charge: its own amount and its own account (separate from the hostel fee account).
            'service_charge_amount' => 'required|numeric|min:0|max:99999999',
            'service_charge_bank_name' => 'required|string|max:255',
            'service_charge_account_number' => 'required|string|max:50',
            'service_charge_account_name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function index()
    {
        if ($redirect = $this->ensureMaster()) {
            return $redirect;
        }

        $campuses = Campus::withCount(['hostels', 'admins'])->orderBy('name')->get();

        return view('superadmin.campuses.index', compact('campuses'));
    }

    /**
     * Choose which campus the admin pages show (or all of them).
     */
    public function switch(Request $request)
    {
        if ($redirect = $this->ensureMaster()) {
            return $redirect;
        }

        $request->validate(['campus_id' => 'nullable|exists:campuses,id']);

        if ($request->filled('campus_id')) {
            session(['active_campus_id' => (int) $request->campus_id]);
            $name = Campus::find($request->campus_id)->name;

            return redirect()->route('dashboard')->with('success', "Now working in {$name}.");
        }

        session()->forget('active_campus_id');

        return redirect()->route('superadmin.campuses.index')->with('success', 'Showing all campuses.');
    }

    public function create()
    {
        if ($redirect = $this->ensureMaster()) {
            return $redirect;
        }

        return view('superadmin.campuses.form', ['campus' => new Campus(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        if ($redirect = $this->ensureMaster()) {
            return $redirect;
        }

        $data = $request->validate($this->rules());
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active');

        Campus::create($data);

        return redirect()->route('superadmin.campuses.index')->with('success', 'Campus created. Now add an admin for it under Admin Management.');
    }

    public function edit(Campus $campus)
    {
        if ($redirect = $this->ensureMaster()) {
            return $redirect;
        }

        return view('superadmin.campuses.form', compact('campus'));
    }

    public function update(Request $request, Campus $campus)
    {
        if ($redirect = $this->ensureMaster()) {
            return $redirect;
        }

        $data = $request->validate($this->rules($campus));
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active');

        $campus->update($data);

        return redirect()->route('superadmin.campuses.index')->with('success', 'Campus updated.');
    }
}
