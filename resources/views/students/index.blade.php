@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Students') }}</h5>
                    <a href="{{ route('students.create') }}" class="btn" style="background-color: #2c3e50; color: white">
                        <i class="fas fa-plus"></i> Add New Student
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Search Bar -->
                    <div class="mb-4">
                        <form action="{{ route('students.index') }}" method="GET" class="row g-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Search students by name, admission #, department, room, gender, or contact number" 
                                           value="{{ request('search') }}"
                                           aria-label="Search students">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn w-100" style="background-color: #2c3e50; color: white">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Admission #</th>
                                    <th>Name</th>
                                    <th>Gender</th> <!-- Added Gender column -->
                                    <th>Department</th>
                                    <th>Semester</th>
                                    <th>Intake</th>
                                    <th>Room</th>
                                    <th>Contact</th>
                                    <th>Check-in Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $student)
                                    <tr>
                                        <td>{{ $student->admission_number }}</td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ ucfirst($student->gender) }}</td> 
                                        <td>{{ $student->department }}</td>
                                        <td>{{ $student->semester }}</td>
                                        <td>{{ $student->intake }}</td>
                                        <td>
                                            @if($student->room)
                                                <span class="badge bg-primary">
                                                    {{ $student->room->room_number }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $student->contact_number }}</td>
                                        <td>{{ $student->check_in_date ? $student->check_in_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <span class="badge rounded-pill {{ $student->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                                                <a href="{{ route('students.show', $student) }}" class="btn btn-info btn-sm" title="View" data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('students.edit', $student) }}" class="btn btn-warning btn-sm" title="Edit" data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?')" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete" data-bs-toggle="tooltip" data-bs-placement="top">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            @if(request()->has('search') && request('search') != '')
                                                No students found matching your search criteria
                                            @else
                                                No students found
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Enhanced Pagination -->
                    @if($students instanceof \Illuminate\Pagination\AbstractPaginator && $students->total() > $students->perPage())
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                            <div class="pagination-info mb-2 mb-md-0">
                                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} results
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $students->appends(request()->query())->links('pagination::bootstrap-4') }}
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
