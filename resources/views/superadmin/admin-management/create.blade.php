@extends('layouts.app')

@section('page-title', 'Create New Admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Create New Admin
                    </h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('superadmin.admin-management.store') }}" method="POST">
                        @csrf

                        <!-- Basic Information -->
                        <h5 class="mb-3">Basic Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror"
                                        id="role"
                                        name="role"
                                        required>
                                    <option value="">Select Role</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <h5 class="mb-3">Password</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       required>
                            </div>
                        </div>

                        <!-- Account Settings -->
                        <h5 class="mb-3">Account Settings</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="is_active"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Account
                                    </label>
                                </div>
                                <small class="form-text text-muted">If unchecked, the admin will not be able to login</small>
                            </div>
                        </div>

                        <!-- Permissions (Optional) -->
                        <h5 class="mb-3">Permissions (Optional)</h5>
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="select_all_permissions"
                                               onchange="toggleAllPermissions()">
                                        <label class="form-check-label fw-bold" for="select_all_permissions">
                                            Select All Permissions
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Room Management</h6>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       id="rooms.view"
                                                       name="permissions[]"
                                                       value="rooms.view"
                                                       {{ in_array('rooms.view', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rooms.view">View Rooms</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       id="rooms.create"
                                                       name="permissions[]"
                                                       value="rooms.create"
                                                       {{ in_array('rooms.create', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rooms.create">Create Rooms</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       id="rooms.edit"
                                                       name="permissions[]"
                                                       value="rooms.edit"
                                                       {{ in_array('rooms.edit', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rooms.edit">Edit Rooms</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <h6>Student Management</h6>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       id="students.view"
                                                       name="permissions[]"
                                                       value="students.view"
                                                       {{ in_array('students.view', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="students.view">View Students</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       id="students.create"
                                                       name="permissions[]"
                                                       value="students.create"
                                                       {{ in_array('students.create', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="students.create">Create Students</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       id="students.edit"
                                                       name="permissions[]"
                                                       value="students.edit"
                                                       {{ in_array('students.edit', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="students.edit">Edit Students</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Leave unchecked to use default permissions for the selected role</small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('superadmin.admin-management.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Admin List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Admin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAllPermissions() {
    const selectAll = document.getElementById('select_all_permissions');
    const checkboxes = document.querySelectorAll('.permission-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = calculatePasswordStrength(password);

    // You can add visual feedback for password strength here
});

function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    return strength;
}
</script>
@endsection
