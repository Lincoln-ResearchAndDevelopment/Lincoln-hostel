@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Visitors') }}
                    <a href="{{ route('visitors.create') }}" class="btn" style="background-color: #2c3e50; color: white">Add New Visitor</a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Search Bar -->
                    <div class="mb-4">
                        <form action="{{ route('visitors.index') }}" method="GET" class="row g-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Search visitors by visitor name or student name..." 
                                        value="{{ request('search') }}"
                                        aria-label="Search visitors">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn w-100" style="background-color: #2c3e50; color: white">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('visitors.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Visitor Name</th>
                                <th>Student</th>
                                <th>ID Number</th>
                                <th>Check-in Time</th>
                                <th>Check-out Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($visitors as $visitor)
                                <tr>
                                    <td>{{ $visitor->visitor_name }}</td>
                                    <td>{{ $visitor->student->full_name }}</td>
                                    <td>{{ $visitor->id_number }}</td>
                                    <td>{{ $visitor->check_in_time_formatted }}</td>
                                    <td>{{ $visitor->check_out_time_formatted }}</td>
                                    <td>
                                        <span class="badge {{ $visitor->check_out_time ? 'bg-secondary' : 'bg-success' }}">
                                            {{ $visitor->check_out_time ? 'Checked Out' : 'Checked In' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                                            <!-- View Button -->
                                            <a href="{{ route('visitors.show', $visitor) }}" 
                                            class="btn btn-info btn-sm"
                                            title="View"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Check Out Button -->
                                            @if(!$visitor->check_out_time)
                                                <form action="{{ route('visitors.update', $visitor) }}" 
                                                    method="POST" 
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" 
                                                            class="btn btn-primary btn-sm"
                                                            title="Check Out"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Delete Button (Admin Only) -->
                                            @if(Auth::user()->isAdmin())
                                                <form action="{{ route('visitors.destroy', $visitor) }}" 
                                                    method="POST" 
                                                    onsubmit="return confirm('Are you sure you want to delete this visitor record?')"
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
                                    <td colspan="7" class="text-center">No visitors found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
