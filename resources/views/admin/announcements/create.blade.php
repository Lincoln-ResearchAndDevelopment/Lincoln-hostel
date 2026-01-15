@extends('layouts.admin')

@section('page-title', 'Create Announcement')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.announcements.index') }}">Announcements</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-bullhorn me-2" style="color: #cc0000;"></i>Create New Announcement</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Announcement title" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="8" placeholder="Write your announcement here..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">You can use basic formatting.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Target Audience <span class="text-danger">*</span></label>
                        <select name="target_audience" class="form-select @error('target_audience') is-invalid @enderror" required>
                            <option value="General" {{ old('target_audience') == 'General' ? 'selected' : '' }}>General (All Students)</option>
                            <option value="Male" {{ old('target_audience') == 'Male' ? 'selected' : '' }}>Boys Only</option>
                            <option value="Female" {{ old('target_audience') == 'Female' ? 'selected' : '' }}>Girls Only</option>
                        </select>
                        @error('target_audience')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select who should see this announcement.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Attachment (Optional)</label>
                        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                        @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Max file size: 10MB. Supported formats: PDF, DOC, DOCX, JPG, PNG</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Publish Announcement
                        </button>
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-lightbulb me-2 text-warning"></i>Tips</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0 small text-muted">
                    <li class="mb-2">Keep titles clear and concise for better visibility</li>
                    <li class="mb-2">Important announcements should be highlighted in the description</li>
                    <li class="mb-2">Attach relevant documents like schedules, forms, or guidelines</li>
                    <li>Announcements will be visible on the student dashboard based on the selected audience</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
