@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Room') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('rooms.update', $room) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-3">
                            <label for="gender_type" class="col-md-4 col-form-label text-md-right">{{ __('Gender Type') }}</label>
                            <div class="col-md-6">
                                <select id="gender_type" class="form-control @error('gender_type') is-invalid @enderror" name="gender_type" required>
                                    <option value="" disabled {{ old('gender_type', $room->gender_type) == '' ? 'selected' : '' }}>Select Gender</option>
                                    <option value="male" {{ old('gender_type', $room->gender_type) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender_type', $room->gender_type) == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="room_number" class="col-md-4 col-form-label text-md-right">{{ __('Room Number') }}</label>
                            <div class="col-md-6">
                                <input id="room_number" type="text" class="form-control @error('room_number') is-invalid @enderror" name="room_number" value="{{ old('room_number', $room->room_number) }}" required autocomplete="room_number" autofocus>
                                @error('room_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="capacity" class="col-md-4 col-form-label text-md-right">{{ __('Capacity') }}</label>
                            <div class="col-md-6">
                                <input id="capacity" type="number" class="form-control @error('capacity') is-invalid @enderror" name="capacity" value="{{ old('capacity', $room->capacity) }}" required min="1">
                                @error('capacity')
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
                                    <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="full" {{ old('status', $room->status) == 'full' ? 'selected' : '' }}>Full</option>
                                    <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description">{{ old('description', $room->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Room') }}
                                </button>
                                <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
