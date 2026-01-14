@extends('layouts.student')

@section('page-title', 'Announcements')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Announcements</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #cc0000 0%, #a30000 100%);">
            <div class="card-body p-4 text-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center p-3 me-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-bullhorn fa-2x" style="color: #cc0000;"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">Announcements & Notices</h2>
                        <p class="mb-0 opacity-75">Stay updated with the latest hostel news and announcements</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements List -->
<div class="row">
    <div class="col-12">
        @if($announcements->count() > 0)
            @foreach($announcements as $announcement)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0 fw-bold">{{ $announcement->title }}</h5>
                        <small class="text-muted">{{ $announcement->created_at->format('M d, Y') }}</small>
                    </div>
                    <p class="card-text text-muted">{{ $announcement->description }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>{{ $announcement->created_at->diffForHumans() }}
                        </small>
                        @if($announcement->hasAttachment())
                            <a href="{{ route('announcements.download', $announcement) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Download Attachment
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $announcements->links() }}
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-bullhorn fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Announcements</h5>
                    <p class="text-muted mb-0">There are no announcements at the moment. Check back later.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
