@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Payment Details') }}
                    <div>
                        <a href="{{ route('payments.edit', $payment) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Receipt Number:</strong> {{ $payment->receipt_number }}</p>
                            <p><strong>Student:</strong> {{ $payment->student->full_name }}</p>
                            <p><strong>Admission Number:</strong> {{ $payment->student->admission_number }}</p>
                            <p><strong>Room:</strong> {{ $payment->student->room ? $payment->student->room->room_number : 'Not Assigned' }}</p>
                            <p><strong>Payment Receipt:</strong></p>
                            @if($payment->receipt_path)
                                @php
                                    // Construct the correct path
                                    $publicPath = 'storage/' . $payment->receipt_path;
                                    $extension = pathinfo($payment->receipt_path, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                    $isPdf = strtolower($extension) === 'pdf';
                                @endphp
                                
                                @if($isImage)
                                    <p>
                                        <img src="{{ asset($publicPath) }}" alt="Receipt Image" style="max-width: 100%; height: auto;" 
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div style="display:none;" class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Receipt image could not be loaded
                                        </div>
                                    </p>
                                @elseif($isPdf)
                                    <p>
                                        <iframe src="{{ asset($publicPath) }}" style="width: 100%; height: 500px;" frameborder="0"></iframe>
                                    </p>
                                @else
                                    <p>Unsupported file type</p>
                                @endif
                                
                                <a href="{{ asset($publicPath) }}" class="btn" style="background-color: #2c3e50; color: white" download>
                                    <i class="fas fa-download me-1"></i> Download Receipt
                                </a>
                            @else
                                <p>No receipt uploaded</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Amount:</strong> {{ number_format($payment->amount, 2) }}</p>
                            <p><strong>Payment Date:</strong> {{ $payment->payment_date->format('M d, Y') }}</p>
                            <p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                            <p>
                                <strong>Status:</strong>
                                <span class="badge 
                                    @if($payment->status == 'completed') bg-success 
                                    @elseif($payment->status == 'pending') bg-warning
                                    @else bg-danger @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Notes:</strong> {{ $payment->notes ?: 'No notes available' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
