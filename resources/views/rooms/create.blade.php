@extends('layouts.admin')

@section('page-title', 'Add New Room')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Add New Room</h5>
                        <a href="{{ route('rooms.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('rooms.store') }}" method="POST">
                        @csrf

                        <!-- Hostel Selection Section -->
                        <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3">Location & Basic Info</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="hostel_id" class="form-label">Select Hostel <span class="text-danger">*</span></label>
                                <select class="form-select @error('hostel_id') is-invalid @enderror" id="hostel_id" name="hostel_id" required>
                                    <option value="">Choose Hostel...</option>
                                    @foreach($hostels as $hostel)
                                        <option value="{{ $hostel->id }}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>
                                            {{ $hostel->name }} ({{ $hostel->code }}) - {{ ucfirst($hostel->type) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('hostel_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-3">
                                <label for="room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('room_number') is-invalid @enderror" 
                                       id="room_number" name="room_number" value="{{ old('room_number') }}" required placeholder="e.g. A-101">
                                @error('room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="floor_number" class="form-label">Floor Number <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('floor_number') is-invalid @enderror" 
                                       id="floor_number" name="floor_number" value="{{ old('floor_number', 1) }}" required min="0">
                                @error('floor_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Type & Pricing Section -->
                        <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3">Type, Capacity & Pricing</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="room_type" class="form-label">Room Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('room_type') is-invalid @enderror" id="room_type" name="room_type" required>
                                    <option value="single" {{ old('room_type') == 'single' ? 'selected' : '' }}>Single Room</option>
                                    <option value="double" {{ old('room_type') == 'double' ? 'selected' : '' }}>Double Room</option>
                                    <option value="triple" {{ old('room_type') == 'triple' ? 'selected' : '' }}>Triple Room</option>
                                    <option value="quad" {{ old('room_type') == 'quad' ? 'selected' : '' }}>Quad Room</option>
                                    <option value="dormitory" {{ old('room_type') == 'dormitory' ? 'selected' : '' }}>Dormitory</option>
                                </select>
                                @error('room_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                       id="capacity" name="capacity" value="{{ old('capacity', 2) }}" required min="1">
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="price_per_semester" class="form-label">Price/Semester (₦) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control @error('price_per_semester') is-invalid @enderror" 
                                           id="price_per_semester" name="price_per_semester" value="{{ old('price_per_semester') }}" required step="0.01" min="0">
                                </div>
                                @error('price_per_semester')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="price_per_year" class="form-label">Price/Year (₦) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control @error('price_per_year') is-invalid @enderror" 
                                           id="price_per_year" name="price_per_year" value="{{ old('price_per_year') }}" required step="0.01" min="0">
                                </div>
                                @error('price_per_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status & Facilities Section -->
                        <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3">Status & Features</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="gender_type" class="form-label">Gender Allocation <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender_type') is-invalid @enderror" id="gender_type" name="gender_type" required>
                                    <option value="male" {{ old('gender_type') == 'male' ? 'selected' : '' }}>Male Only</option>
                                    <option value="female" {{ old('gender_type') == 'female' ? 'selected' : '' }}>Female Only</option>
                                </select>
                                @error('gender_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label">Current Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                    <option value="full" {{ old('status') == 'full' ? 'selected' : '' }}>Full</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block">Room Facilities</label>
                            <div class="row g-3">
                                @foreach(['AC', 'Fan', 'WiFi', 'Attached Bathroom', 'Balcony', 'Reading Table', 'Wardrobe', 'Water Heater'] as $facility)
                                    <div class="col-md-3 col-6">
                                        <div class="form-check card p-2 h-100">
                                            <input class="form-check-input ms-1" type="checkbox" name="facilities[]" 
                                                   value="{{ $facility }}" id="facility_{{ Str::slug($facility) }}"
                                                   {{ in_array($facility, old('facilities', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2 w-100" style="cursor: pointer;" for="facility_{{ Str::slug($facility) }}">
                                                {{ $facility }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Additional Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-light me-md-2">Reset</button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Create Room
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-adjust capacity based on room type
document.getElementById('room_type').addEventListener('change', function() {
    const capacities = {
        'single': 1,
        'double': 2,
        'triple': 3,
        'quad': 4,
        'dormitory': 8
    };
    const capacityInput = document.getElementById('capacity');
    if (capacities[this.value]) {
        capacityInput.value = capacities[this.value];
    }
});
</script>
@endsection
