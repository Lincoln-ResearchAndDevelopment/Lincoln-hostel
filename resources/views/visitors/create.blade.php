@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add New Visitor') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('visitors.store') }}">
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
                        @else
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                        @endif

                        <div class="form-group row mb-3">
                            <label for="visitor_name" class="col-md-4 col-form-label text-md-right">{{ __('Visitor Name') }}</label>
                            <div class="col-md-6">
                                <input id="visitor_name" type="text" class="form-control @error('visitor_name') is-invalid @enderror" name="visitor_name" value="{{ old('visitor_name') }}" required>
                                @error('visitor_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_number" class="col-md-4 col-form-label text-md-right">{{ __('ID Number') }}</label>
                            <div class="col-md-6">
                                <input id="id_number" type="text" class="form-control @error('id_number') is-invalid @enderror" name="id_number" value="{{ old('id_number') }}" required>
                                @error('id_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="purpose" class="col-md-4 col-form-label text-md-right">{{ __('Purpose') }}</label>
                            <div class="col-md-6">
                                <textarea id="purpose" class="form-control @error('purpose') is-invalid @enderror" name="purpose" required>{{ old('purpose') }}</textarea>
                                @error('purpose')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn" style="background-color: #2c3e50; color: white">
                                    {{ __('Check In Visitor') }}
                                </button>
                                <a href="{{ route('visitors.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
