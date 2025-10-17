@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add New Complaint') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('complaints.store') }}">
                        @csrf

                        @if(Auth::user()->isAdmin())
                            <div class="form-group row mb-3">
                                <label for="student_id" class="col-md-4 col-form-label text-md-right">{{ __('Student') }}</label>
                                <div class="col-md-6">
                                    <select id="student_id" class="form-control @error('student_id') is-invalid @enderror" name="student_id" required>
                                        <option value="">Select a student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('student_id', request('student_id')) == $student->id ? 'selected' : '' }}>
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
                        @endif

                        <div class="form-group row mb-3">
                            <label for="subject" class="col-md-4 col-form-label text-md-right">{{ __('Subject') }}</label>
                            <div class="col-md-6">
                                <input id="subject" type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{ old('subject') }}" required>
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
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn" style="background-color: #2c3e50; color: white">
                                    {{ __('Submit') }}
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
