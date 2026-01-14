
@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 text-dark">Staff Management</h1>
                <a href="{{ route('staff.create') }}" class="btn btn-dark">
                    <i class="fas fa-plus me-2"></i>Add New Staff
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="border-0">Name</th>
                                    <th class="border-0">Contact</th>
                                    <th class="border-0">Staff Gender</th>
                                    <th class="border-0">Assigned Hostel</th>
                                    <th class="border-0 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staff as $member)
                                    <tr>
                                        <td class="align-middle">{{ $member->name }}</td>
                                        <td class="align-middle">{{ $member->contact }}</td>
                                        <td class="align-middle">
                                            <span class="badge bg-secondary text-capitalize">
                                                {{ $member->staff_gender }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge bg-dark text-capitalize">
                                                {{ $member->assigned_hostel_gender }} Hostel
                                            </span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('staff.edit', $member) }}" class="btn btn-outline-dark btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('staff.destroy', $member) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                            onclick="return confirm('Are you sure you want to delete this staff member?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            No staff members found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    padding: 1rem 0.75rem;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.btn-group .btn {
    border-radius: 4px;
    margin-left: 0.25rem;
}
</style>
@endsection