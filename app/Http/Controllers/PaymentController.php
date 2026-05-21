<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\TracksEmails;

class PaymentController extends Controller
{
    use TracksEmails;
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'student') {
            Payment::where('is_read', false)->update(['is_read' => true]);
        }

        $query = Payment::with('student')->latest();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('full_name', 'like', "%$search%");
                })
                ->orWhere('receipt_number', 'like', "%$search%")
                ->orWhere('amount', 'like', "%$search%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type === 'booking') {
            $query->whereNotNull('room_id');
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
        $payment->load(['student', 'room.hostel']);
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

    public function approve(Payment $payment)
    {
        // Handle Room Booking Assignment if applicable
        if ($payment->room_id && $payment->payment_plan) {
            $student = $payment->student;
            $room = \App\Models\Room::find($payment->room_id);

            // Validation checks before approval
            if (!$room) {
                return redirect()->back()->with('error', 'Room not found. Cannot approve payment.');
            }

            if ($student->room_id) {
                return redirect()->back()->with('error', 'Student already has a room assigned. Cannot approve this booking.');
            }

            // CRITICAL: Refresh room data and validate capacity
            $room->refresh();
            if ($room->occupied >= $room->capacity) {
                // Update payment status to failed
                $payment->update(['status' => 'failed']);
                
                // Notify student about the issue
                \App\Models\Notification::notifyStudent(
                    $payment->student_id,
                    'payment',
                    'Room Booking Failed',
                    'Unfortunately, Room ' . $room->room_number . ' in ' . $room->hostel->name . ' is now full. Your payment has been marked as failed. Please contact admin for a refund or to select another room.',
                    ['payment_id' => $payment->id]
                );
                
                return redirect()->back()->with('error', 'Room is now full. Payment marked as failed and student notified.');
            }

            // All validations passed - proceed with assignment
            \DB::transaction(function () use ($payment, $student, $room) {
                // Update payment status
                $payment->update(['status' => 'completed']);

                // Assign Room
                $student->room_id = $room->id;
                $student->check_in_date = now();
                
                // Set fee and mark as paid since this is the booking payment
                $student->hostel_fee_amount = $payment->amount;
                $student->hostel_fee_paid = ($student->hostel_fee_paid ?? 0) + $payment->amount;
                $student->hostel_fee_status = 'paid';
                $student->save();

                // Update Room Occupancy
                $room->increment('occupied');
                
                // Check if room is now full and update status
                if ($room->occupied >= $room->capacity) {
                    $room->status = 'full';
                    $room->save();
                }

                // Create attendance record for check-in
                \App\Models\AttendanceRecord::create([
                    'student_id' => $student->id,
                    'type' => 'check_in',
                    'recorded_at' => now(),
                    'recorded_by' => 'Admin (Auto)',
                    'notes' => 'Room ' . $room->room_number . ' in ' . $room->hostel->name . ' assigned after booking payment approval',
                    'location' => 'Admin Dashboard',
                ]);

                \Log::info("Room booking approved and assigned", [
                    'student_id' => $student->id,
                    'room_id' => $room->id,
                    'hostel' => $room->hostel->name,
                    'room_number' => $room->room_number,
                    'new_occupancy' => $room->occupied . '/' . $room->capacity
                ]);
            });

            // Notify Student of successful assignment
            \App\Models\Notification::notifyStudent(
                $payment->student_id,
                'payment',
                'Room Successfully Assigned! 🎉',
                'Congratulations! Your payment of ₦' . number_format($payment->amount, 2) . ' has been approved. You have been assigned to Room ' . $room->room_number . ' in ' . $room->hostel->name . '. Welcome to your new home!',
                ['payment_id' => $payment->id, 'room_id' => $room->id]
            );
            
            // SEND EMAIL NOTIFICATION WITH TRACKING
            try {
                $emailResult = $this->sendTrackedEmail(
                    'room_assigned',
                    $student->email,
                    new \App\Mail\RoomAssignedMail($student, $room),
                    [
                        'payment_id' => $payment->id,
                        'room_id' => $room->id,
                        'student_name' => $student->full_name,
                        'room_number' => $room->room_number,
                        'operation' => 'room_assignment_notification'
                    ]
                );
                $this->logEmailResult($emailResult, 'Room Assignment Notification');
            } catch (\Exception $e) {
                \Log::error('❌ ROOM ASSIGNED EMAIL FAILED', [
                    'payment_id' => $payment->id,
                    'student_id' => $student->id,
                    'room_id' => $room->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Notify Parent (Rule 2.5)
            $this->notifyParentOfPayment($student, "LincHostel: Your ward {$student->full_name}'s room booking payment has been approved. Room {$room->room_number} assigned.");

            return redirect()->back()->with('success', 'Payment approved! Room ' . $room->room_number . ' has been assigned to ' . $student->full_name . '.');
            
        } else {
            // Regular payment (not a booking)
            \DB::transaction(function () use ($payment) {
                $payment->update(['status' => 'completed']);
                $student = $payment->student;
                
                // Update student balances
                $student->hostel_fee_paid = ($student->hostel_fee_paid ?? 0) + $payment->amount;
                
                // Update fee status if applicable
                if ($student->hostel_fee_paid >= $student->hostel_fee_amount) {
                     $student->hostel_fee_status = 'paid';
                } elseif ($student->hostel_fee_paid > 0) {
                     $student->hostel_fee_status = 'partial';
                }
                
                $student->save();
            });

            $student = $payment->student;

            // Notify Student
            \App\Models\Notification::notifyStudent(
                $payment->student_id,
                'payment',
                'Payment Approved',
                'Your payment of ₦' . number_format($payment->amount, 2) . ' has been approved and processed successfully.',
                ['payment_id' => $payment->id]
            );

            // SEND EMAIL NOTIFICATION WITH TRACKING
            try {
                $emailResult = $this->sendTrackedEmail(
                    'payment_approved',
                    $student->email,
                    new \App\Mail\GeneralPaymentApprovedMail($payment),
                    [
                        'payment_id' => $payment->id,
                        'student_name' => $student->full_name,
                        'amount' => $payment->amount,
                        'operation' => 'general_payment_approval_notification'
                    ]
                );
                $this->logEmailResult($emailResult, 'General Payment Approval Notification');
            } catch (\Exception $e) {
                \Log::error('❌ GENERAL PAYMENT APPROVED EMAIL FAILED', [
                    'payment_id' => $payment->id,
                    'student_id' => $student->id,
                    'amount' => $payment->amount,
                    'error' => $e->getMessage()
                ]);
            }

            // Notify Parent (Rule 2.5)
            $this->notifyParentOfPayment($student, "LincHostel: Your ward {$student->full_name}'s payment of ₦" . number_format($payment->amount, 2) . " has been approved.");

            return redirect()->back()->with('success', 'Payment approved successfully.');
        }
    }

    /**
     * Notify parent with failover logic (Rule 2.5)
     */
    private function notifyParentOfPayment($student, $message)
    {
        try {
            $student->load('hostelApplication');
            $parentPhone = $student->parent_phone;

            // Failover to hostel_applications (Rule 2.5)
            if ((empty($parentPhone) || $parentPhone === 'N/A') && $student->hostelApplication) {
                $parentPhone = $student->hostelApplication->parent_phone;
            }

            if (!empty($parentPhone) && $parentPhone !== 'N/A') {
                $smsService = new \App\Services\SmsService();
                $smsService->sendSms($parentPhone, $message);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send parental payment notification: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Payment $payment)
    {
        $payment->update(['status' => 'failed']);

        // Notify Student
        \App\Models\Notification::notifyStudent(
            $payment->student_id,
            'payment',
            'Payment Rejected',
            'Your payment of ₦' . number_format($payment->amount, 2) . ' has been rejected.',
            ['payment_id' => $payment->id]
        );

        return redirect()->back()->with('success', 'Payment rejected successfully.');
    }
}
