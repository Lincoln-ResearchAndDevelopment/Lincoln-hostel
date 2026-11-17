<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SemesterPayment;
use App\Models\Student;
use Illuminate\Http\Request;

class SemesterPaymentController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', 'pending');

        if ($view === 'outstanding') {
            $students = Student::where('semesters_to_pay', '>', 1)->with('semesterPayments')->orderBy('name')->get()
                ->filter(fn ($s) => 1 + $s->semesterPayments->where('status', 'approved')->count() < $s->semesters_to_pay)
                ->values();

            return view('admin.semester-payments.index', ['view' => $view, 'students' => $students, 'payments' => collect()]);
        }

        $payments = SemesterPayment::with('student')
            ->when($view !== 'all', fn ($q) => $q->where('status', $view))
            ->orderByDesc('updated_at')->paginate(25)->withQueryString();

        return view('admin.semester-payments.index', ['view' => $view, 'students' => collect(), 'payments' => $payments]);
    }

    public function approve(SemesterPayment $semesterPayment)
    {
        $semesterPayment->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => null,
        ]);

        return back()->with('success', 'Semester ' . $semesterPayment->semester_number . ' payment approved.');
    }

    public function reject(Request $request, SemesterPayment $semesterPayment)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:1000']);

        $semesterPayment->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Semester ' . $semesterPayment->semester_number . ' payment rejected.');
    }
}
