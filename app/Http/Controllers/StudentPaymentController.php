<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentPaymentController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('Payment form submitted');
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:bank_transfer,credit_card,other',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $student = auth('student')->user();
        if (!$student) {
            \Log::error('No student authenticated via student guard.');
            return back()->with('error', 'No student record found for this user.');
        }

        // Create the payment record first
        $payment = Payment::create([
            'student_id' => $student->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'],
            'status' => 'pending',
            'payment_date' => now(),
            'receipt_number' => 'RCPT-'.strtoupper(uniqid()),
        ]);

        // Handle file upload using the same method as admin
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $fileName = 'receipt_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store in storage/app/public/receipts (same as admin)
            $filePath = $file->storeAs('receipts', $fileName, 'public');
            
            // Log for debugging
            \Log::info('Student file stored at: ' . $filePath);
            \Log::info('Full storage path: ' . storage_path('app/public/' . $filePath));
            
            // Update the payment with the file path
            $payment->receipt_path = $filePath;
            $payment->save();
        } else {
            return back()->with('error', 'Receipt file is required');
        }

        return redirect()->route('student.dashboard')
                    ->with('payment_success', 'Payment submitted successfully!');
    }
}