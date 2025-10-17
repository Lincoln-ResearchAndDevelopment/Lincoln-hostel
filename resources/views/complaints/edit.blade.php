@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Complaint') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('complaints.update', $complaint) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-3">
                            <label for="student_id" class="col-md-4 col-form-label text-md-right">{{ __('Student') }}</label>
                            <div class="col-md-6">
                                <select id="student_id" class="form-control @error('student_id') is-invalid @enderror" name="student_id" required>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id', $complaint->student_id) == $student->id ? 'selected' : '' }}>
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
                        </div>

                        <div class="form-group row mb-3">
                            <label for="subject" class="col-md-4 col-form-label text-md-right">{{ __('Subject') }}</label>
                            <div class="col-md-6">
                                <input id="subject" type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{ old('subject', $complaint->subject) }}" required>
                                @error('subject')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" required>{{ old('description', $complaint->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                            <div class="col-md-6">
                                <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" required>
                                    <option value="submitted" {{ old('status', $complaint->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="in progress" {{ old('status', $complaint->status) == 'in progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ old('status', $complaint->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="resolution" class="col-md-4 col-form-label text-md-right">{{ __('Resolution') }}</label>
                            <div class="col-md-6">
                                <textarea id="resolution" class="form-control @error('resolution') is-invalid @enderror" name="resolution">{{ old('resolution', $complaint->resolution) }}</textarea>
                                @error('resolution')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Complaint') }}
                                </button>
                                <a href="{{ route('complaints.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
