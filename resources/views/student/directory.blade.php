@extends('layouts.student')

@section('page-title', 'Student Directory')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
            <div class="card-body p-4" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);">
                <div class="row align-items-center">
                    <div class="col-md-6 text-white">
                        <h3 class="fw-bold mb-1">Student Community</h3>
                        <p class="opacity-75 mb-0">Connect and collaborate with your peers across programs.</p>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <form action="{{ route('student.directory') }}" method="GET" class="row g-2">
                            <div class="col-sm-7">
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-white shadow-sm"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-0 shadow-sm" placeholder="Search by name..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <select name="program" class="form-select border-0 shadow-sm" onchange="this.form.submit()">
                                    <option value="">All Programs</option>
                                    @foreach($programs as $prog)
                                        <option value="{{ $prog->name }}" {{ request('program') == $prog->name ? 'selected' : '' }}>{{ $prog->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @forelse($students as $entry)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 student-card" style="border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="position-relative mb-3 d-inline-block">
                        <img src="{{ $entry->profile_photo_url }}" 
                             alt="{{ $entry->full_name }}" 
                             class="rounded-circle shadow-sm" 
                             style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #f8f9fa;">
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle" title="Active Student"></span>
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-1">{{ $entry->full_name }}</h5>
                    <div class="badge bg-soft-primary text-primary mb-3 px-3 py-2 rounded-pill" style="background-color: rgba(52, 152, 219, 0.1);">
                        <i class="fas fa-graduation-cap me-1 small"></i> {{ $entry->program }}
                    </div>
                    
                    <div class="d-flex justify-content-center gap-4 text-muted small border-top pt-3">
                        <div>
                            <div class="fw-bold text-dark mb-0">Semester</div>
                            <div>{{ $entry->semester }}</div>
                        </div>
                        <div class="vr"></div>
                        <div>
                            <div class="fw-bold text-dark mb-0">Intake</div>
                            <div>{{ $entry->intake }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="mb-3">
                <i class="fas fa-users-slash fa-4x text-muted opacity-25"></i>
            </div>
            <h4 class="text-muted">No students found</h4>
            <p class="text-muted mb-4">Try adjusting your search or filters.</p>
            <a href="{{ route('student.directory') }}" class="btn btn-outline-primary px-4 rounded-pill">View All Students</a>
        </div>
    @endforelse
</div>

<div class="mt-5 d-flex justify-content-center">
    {{ $students->links() }}
</div>

<style>
    .student-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .bg-soft-primary {
        background-color: #e3f2fd;
        color: #1976d2;
    }
</style>
@endsection

