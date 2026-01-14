@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Payment') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('payments.update', $payment) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- <div class="form-group row mb-3">
                            <label for="student_id" class="col-md-4 col-form-label text-md-right">{{ __('Student') }}</label>
                            <div class="col-md-6">
                                <select id="student_id" class="form-control @error('student_id') is-invalid @enderror" name="student_id" required>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id', $payment->student_id) == $student->id ? 'selected' : '' }}>
                                            {{ $student->full_name }} ({{ $student->admission_number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}

                        {{-- <div class="form-group row mb-3">
                            <label for="amount" class="col-md-4 col-form-label text-md-right">{{ __('Amount') }}</label>
                            <div class="col-md-6">
                                <input id="amount" type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', $payment->amount) }}" required>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}

                        {{-- <div class="form-group row mb-3">
                            <label for="payment_date" class="col-md-4 col-form-label text-md-right">{{ __('Payment Date') }}</label>
                            <div class="col-md-6">
                                <input id="payment_date" type="date" class="form-control @error('payment_date') is-invalid @enderror" name="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}

                        {{-- <div class="form-group row mb-3">
                            <label for="payment_method" class="col-md-4 col-form-label text-md-right">{{ __('Payment Method') }}</label>
                            <div class="col-md-6">
                                <select id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" name="payment_method" required>
                                    <option value="credit_card" {{ old('payment_method', $payment->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="bank_transfer" {{ old('payment_method', $payment->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                                @error('payment_method')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}

                        {{-- <div class="form-group row mb-3">
                            <label for="receipt_number" class="col-md-4 col-form-label text-md-right">{{ __('Receipt Number') }}</label>
                            <div class="col-md-6">
                                <input id="receipt_number" type="text" class="form-control @error('receipt_number') is-invalid @enderror" name="receipt_number" value="{{ old('receipt_number', $payment->receipt_number) }}" required>
                                @error('receipt_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}

                        {{-- <div class="form-group row mb-3">
                            <label for="receipt" class="col-md-4 col-form-label text-md-right">{{ __('Payment Receipt') }}</label>
                            <div class="col-md-6">
                                <input type="file" id="receipt" class="form-control @error('receipt') is-invalid @enderror" name="receipt" required>
                                <small class="text-muted">JPG, PNG, PDF (Max: 2MB)</small>
                                @error('receipt')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="form-group row mb-3">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                            <div class="col-md-6">
                                <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" required>
                                    <option value="completed" {{ old('status', $payment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group row mb-3">
                            <label for="notes" class="col-md-4 col-form-label text-md-right">{{ __('Notes') }}</label>
                            <div class="col-md-6">
                                <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes">{{ old('notes', $payment->notes) }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Payment') }}
                                </button>
                                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
