@extends('layouts.app')

@section('page-title', 'Admin Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users-cog me-2"></i>
                        Admin Management
                    </h1>
                    <p class="text-muted mb-0">Manage system administrators and their permissions</p>
                </div>
                <a href="{{ route('superadmin.admin-management.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Admin
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_admins'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_admins'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Inactive Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive_admins'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Created by You</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $adminUsers->where('created_by', $superAdmin->id)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Admin Users
                    </h6>
                    <div class="d-flex">
                        <input type="text" id="searchInput" class="form-control form-control-sm me-2" placeholder="Search admins..." style="width: 200px;">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-filter="all">All Admins</a></li>
                                <li><a class="dropdown-item" href="#" data-filter="active">Active Only</a></li>
                                <li><a class="dropdown-item" href="#" data-filter="inactive">Inactive Only</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="adminTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adminUsers as $admin)
                                    <tr data-status="{{ $admin->is_active ? 'active' : 'inactive' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle-sm me-2">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $admin->name }}</strong>
                                                    @if($admin->phone)
                                                        <br><small class="text-muted">{{ $admin->phone }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $admin->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $admin->role === 'admin' ? 'primary' : ($admin->role === 'manager' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($admin->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($admin->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $admin->created_at->format('M d, Y') }}</small>
                                            @if($admin->created_by_user)
                                                <br><small class="text-muted">by {{ $admin->created_by_user->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($admin->last_login_at)
                                                <small>{{ $admin->last_login_at->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted">Never</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('superadmin.admin-management.show', $admin) }}"
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('superadmin.admin-management.edit', $admin) }}"
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($admin->is_active)
                                                    <form action="{{ route('superadmin.admin-management.destroy', $admin) }}"
                                                          method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this admin?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Deactivate">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('superadmin.admin-management.activate', $admin) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Activate">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No admin users found</p>
                                            <a href="{{ route('superadmin.admin-management.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Create First Admin
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($adminUsers->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $adminUsers->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle-sm {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #007bff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.badge {
    font-size: 0.75rem;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#adminTable tbody tr');

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Filter functionality
    const filterLinks = document.querySelectorAll('[data-filter]');
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-filter');

            tableRows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                } else {
                    const status = row.getAttribute('data-status');
                    if (status === filter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    });
});
</script>
@endsection
