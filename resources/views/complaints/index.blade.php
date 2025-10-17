@extends('layouts.app')

@section('content')
<style>
/* Custom Pagination Styles */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border: 1px solid #dee2e6;
    color: #6c757d;
    background-color: #fff;
    border-radius: 0.25rem;
    margin: 0 2px;
    transition: all 0.15s ease-in-out;
}

.pagination .page-link:hover {
    color: #495057;
    background-color: #e9ecef;
    border-color: #dee2e6;
    text-decoration: none;
}

.pagination .page-item.active .page-link {
    background-color: #2c3e50;
    border-color: #2c3e50;
    color: #fff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

.pagination .page-link:focus {
    box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
    outline: 0;
}

/* Small pagination variant */
.pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
}

/* Pagination info text */
.pagination-info {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 1rem;
}
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Complaints') }}
                    <a href="{{ route('complaints.create') }}" class="btn" style="background-color: #2c3e50; color: white">Add New Complaint</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Search Bar -->
                    <div class="mb-4">
                        <form action="{{ route('complaints.index') }}" method="GET" class="row g-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Search complaints by name or subject..." 
                                        value="{{ request('search') }}"
                                        aria-label="Search complaints">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn w-100" style="background-color: #2c3e50; color: white">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Student</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($complaints as $complaint)
                                <tr>
                                    <td>{{ Str::limit($complaint->subject, 30) }}</td>
                                    <td>{{ $complaint->student->full_name }}</td>
                                    <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($complaint->status == 'resolved') bg-success 
                                            @elseif($complaint->status == 'submitted') bg-secondary
                                            @else bg-warning @endif">
                                            {{ ucfirst($complaint->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                                            <!-- View Button -->
                                            <a href="{{ route('complaints.show', $complaint) }}" 
                                            class="btn btn-info btn-sm"
                                            title="View"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if(Auth::user()->isAdmin())
                                                <!-- Edit Button -->
                                                <a href="{{ route('complaints.edit', $complaint) }}" 
                                                class="btn btn-warning btn-sm"
                                                title="Edit"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Delete Button -->
                                                <form action="{{ route('complaints.destroy', $complaint) }}" 
                                                    method="POST" 
                                                    onsubmit="return confirm('Are you sure you want to delete this complaint?')"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm"
                                                            title="Delete"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No complaints found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <!-- Enhanced Pagination -->
                    @if($complaints instanceof \Illuminate\Pagination\AbstractPaginator && $complaints->total() > $complaints->perPage())
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                            <!-- Pagination Info -->
                            <div class="pagination-info mb-2 mb-md-0">
                                Showing {{ $complaints->firstItem() }} to {{ $complaints->lastItem() }} of {{ $complaints->total() }} results
                            </div>
                            
                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $complaints->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection