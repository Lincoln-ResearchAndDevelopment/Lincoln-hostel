<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Mail\HostelApplicationMail;
use Illuminate\Support\Facades\Mail;

class HostelApplicationController extends Controller
{

    public function create()
    {
        return view('apply');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $validated = $request->validate([
            'academic_year'         => 'required|string|max:255',
            'passport_photo'        => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'name'                  => 'required|string|max:255',
            'reg_number'            => 'required|string|max:255',
            'intake'                => 'required|string|max:255',
            'program'               => 'required|string|max:255',
            'department'            => 'required|string|max:255',
            'medical_condition'     => 'nullable|string|max:255',
            'emergency_contact'     => 'required|string|max:255',
            'declaration_name'      => 'required|string|max:255',
            'applicant_signature'   => 'required|string|max:255',
            'date'                  => 'required|date',
            'guardian_signature'    => 'required|string|max:255',
            'guardian_date'         => 'required|date',
            'amount_paid'           => 'required|string|max:255',
        ]);

        // Handle passport upload
        if ($request->hasFile('passport_photo')) {
            $file = $request->file('passport_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/passports'), $filename);
            $validated['passport_photo'] = asset('uploads/passports/' . $filename);
        } else {
            $validated['passport_photo'] = null;
        }

        // Handle Application Form Receipt upload
        if ($request->hasFile('applicationform_receipt')) {
            $file = $request->file('applicationform_receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $validated['applicationform_receipt'] = asset('uploads/receipts/' . $filename);
        } else {
            $validated['applicationform_receipt'] = null;
        }

        // Handle Hostel Fee Receipt upload
        if ($request->hasFile('hostelfee_receipt')) {
            $file = $request->file('hostelfee_receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $validated['hostelfee_receipt'] = asset('uploads/receipts/' . $filename);
        } else {
            $validated['hostelfee_receipt'] = null;
        }

        // Try to send email and catch any possible errors
        try {
            Mail::to('lotannaemmanuelotikpo@gmail.com')->send(new HostelApplicationMail($validated));

            return redirect()->back()->with('success', 'Application submitted successfully! We will get back to you shortly.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong while sending your application. Please try again.');
        }
    }
}
