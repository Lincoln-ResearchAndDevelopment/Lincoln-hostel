<?php

namespace App\Http\Controllers;

use App\Models\SemesterPayment;
use Illuminate\Http\Request;

class StudentSemesterPaymentController extends Controller
{
    public function store(Request $request)
    {
        $student = auth()->guard('student')->user();

        $validated = $request->validate([
            'semester_number' => 'required|integer|min:2|max:12',
            'service_charge_receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'hostel_fee_receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $entry = collect($student->semesterSchedule())->firstWhere('semester', (int) $validated['semester_number']);
        if (!$entry || !$entry['can_upload']) {
            return back()->with('error', 'You cannot upload a receipt for that semester yet.');
        }

        $store = fn ($file) => 'storage/' . $file->store('semester_payments/' . $student->id, 'public');

        SemesterPayment::withoutGlobalScopes()->updateOrCreate(
            ['student_id' => $student->id, 'semester_number' => $validated['semester_number']],
            [
                'campus_id' => $student->campus_id,
                'service_charge_receipt' => $store($request->file('service_charge_receipt')),
                'hostel_fee_receipt' => $store($request->file('hostel_fee_receipt')),
                'status' => 'pending',
                'admin_notes' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]
        );

        return back()->with('success', 'Receipts submitted for semester ' . $validated['semester_number'] . '. They will show as paid once the hostel office approves them.');
    }
}
