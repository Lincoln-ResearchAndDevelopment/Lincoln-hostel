@extends('layouts.app')

@section('page-title', 'Edit Admin - ' . $adminUser->name)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Edit Admin: {{ $adminUser->name }}
                        </h3>
                        <a href="{{ route('superadmin.admin-management.show', $adminUser) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Details
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('superadmin.admin-management.update', $adminUser) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <h5 class="mb-3">Basic Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $adminUser->name) }}"
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
                                       value="{{ old('email', $adminUser->email) }}"
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
                                       value="{{ old('phone', $adminUser->phone) }}">
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
                                    <option value="admin" {{ old('role', $adminUser->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="manager" {{ old('role', $adminUser->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="staff" {{ old('role', $adminUser->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password (Optional) -->
                        <h5 class="mb-3">Change Password (Optional)</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="Leave blank to keep current password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Leave blank to keep current password">
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
                                           {{ old('is_active', $adminUser->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Account
                                    </label>
                                </div>
                                <small class="form-text text-muted">If unchecked, the admin will not be able to login</small>
                            </div>
                        </div>

                        <!-- Permissions -->
                        <h5 class="mb-3">Permissions</h5>
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
                                                       {{ in_array('rooms.view', old('permissions', $adminUser->permissions ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rooms.view">View Rooms</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       id="rooms.create"
                                                       name="permissions[]"
                                                       value="rooms.create"
                                                       {{ in_array('rooms.create', old('permissions', $adminUser->permissions ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rooms.create">Create Rooms</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       id="rooms.edit"
                                                       name="permissions[]"
                                                       value="rooms.edit"
                                                       {{ in_array('rooms.edit', old('permissions', $adminUser->permissions ?? [])) ? 'checked' : '' }}>
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
                                                       {{ in_array('students.view', old('permissions', $adminUser->permissions ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="students.view">View Students</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       id="students.create"
                                                       name="permissions[]"
                                                       value="students.create"
                                                       {{ in_array('students.create', old('permissions', $adminUser->permissions ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="students.create">Create Students</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       id="students.edit"
                                                       name="permissions[]"
                                                       value="students.edit"
                                                       {{ in_array('students.edit', old('permissions', $adminUser->permissions ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="students.edit">Edit Students</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('superadmin.admin-management.show', $adminUser) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Details
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Admin
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
</script>
@endsection
