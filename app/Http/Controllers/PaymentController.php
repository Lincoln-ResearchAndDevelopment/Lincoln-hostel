<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'student') {
            Payment::where('is_read', false)->update(['is_read' => true]);
        }

        $query = Payment::with('student')->latest();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%");
            })
            ->orWhere('receipt_number', 'like', "%$search%")
            ->orWhere('amount', 'like', "%$search%");
        }

        $payments = $query->paginate(10);

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $students = Student::where('status', 'active')->get();
        return view('payments.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'receipt_number' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'student_id' => $request->student_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $fileName = 'receipt_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store in storage/app/public/receipts (consistent location)
            $filePath = $file->storeAs('receipts', $fileName, 'public');
            
            // Store only the relative path (e.g., "receipts/filename.jpg")
            $payment->receipt_path = $filePath;
            $payment->save();
        }

        return redirect()->route('payments.index')->with('success', 'Payment added successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('student');
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $students = Student::where('status', 'active')->get();
        return view('payments.edit', compact('payment', 'students'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'receipt_number' => 'required|string|unique:payments,receipt_number,' . $payment->id,
            'status' => 'required|in:pending,completed,failed',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            // Delete old file
            if ($payment->receipt_path && Storage::disk('public')->exists($payment->receipt_path)) {
                Storage::disk('public')->delete($payment->receipt_path);
            }

            $file = $request->file('receipt');
            $fileName = 'receipt_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('receipts', $fileName, 'public');

            $validated['receipt_path'] = $filePath;
        }

        $payment->update($validated);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->receipt_path && Storage::disk('public')->exists($payment->receipt_path)) {
            Storage::disk('public')->delete($payment->receipt_path);
        }

        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully');
    }
}
