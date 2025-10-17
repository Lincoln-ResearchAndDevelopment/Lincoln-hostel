@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Student Details') }}
                    <div>
                        <a href="{{ route('students.edit', $student) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Admission Number:</strong> {{ $student->admission_number }}</p>
                            <p><strong>Full Name:</strong> {{ $student->full_name }}</p>
                            <p><strong>Gender:</strong> {{ $student->gender ?? 'Not Specified' }}</p>
                            <p><strong>Department:</strong> {{ $student->department }}</p>
                            <p><strong>Semester:</strong> Semester {{ $student->semester }}</p>
                            <p><strong>Intake:</strong> {{ $student->intake }}</p>
                            <p><strong>Room:</strong> {{ $student->room ? $student->room->room_number : 'Not Assigned' }}</p>
                            <p><strong>Contact Number:</strong> {{ $student->contact_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Emergency Contact:</strong> {{ $student->emergency_contact }}</p>
                            <p><strong>Address:</strong> {{ $student->address }}</p>
                            <p><strong>Check-in Date:</strong> {{ $student->check_in_date->format('M d, Y') }}</p>
                            <p><strong>Expected Check-out Date:</strong> {{ $student->expected_check_out_date->format('M d, Y') }}</p>
                            <p>
                                <strong>Status:</strong>
                                <span class="badge {{ $student->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </p>
                            <p><strong>Email:</strong> {{ $student->user->email }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mt-4">
                        <!-- Payments Section -->
                        <div class="col-md-6">
                            <h5>Payment History</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($student->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if($payment->status == 'completed') bg-success 
                                                    @elseif($payment->status == 'pending') bg-warning
                                                    @else bg-danger @endif">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No payment records</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <a href="{{ route('payments.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary">Add Payment</a>
                        </div>

                        <!-- Complaints Section -->
                        <div class="col-md-6">
                            <h5>Complaints</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($student->complaints as $complaint)
                                        <tr>
                                            <td>{{ Str::limit($complaint->subject, 20) }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if($complaint->status == 'resolved') bg-success 
                                                    @elseif($complaint->status == 'submitted') bg-secondary
                                                    @else bg-warning @endif">
                                                    {{ ucfirst($complaint->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No complaints</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <a href="{{ route('complaints.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary">Add Complaint</a>
                        </div>
                    </div>

                    <!-- Visitors Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Visitors</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Visitor Name</th>
                                        <th>ID Number</th>
                                        <th>Check-in Time</th>
                                        <th>Check-out Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($student->visitors as $visitor)
                                        <tr>
                                            <td>{{ $visitor->visitor_name }}</td>
                                            <td>{{ $visitor->id_number }}</td>
                                            <td>{{ $visitor->check_in_time->format('M d, Y H:i') }}</td>
                                            <td>{{ $visitor->check_out_time ? $visitor->check_out_time->format('M d, Y H:i') : '-' }}</td>
                                            <td>
                                                <span class="badge {{ $visitor->check_out_time ? 'bg-secondary' : 'bg-success' }}">
                                                    {{ $visitor->check_out_time ? 'Checked Out' : 'Checked In' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No visitors</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <a href="{{ route('visitors.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary">Add Visitor</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection