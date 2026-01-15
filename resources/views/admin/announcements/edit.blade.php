@extends('layouts.admin')

@section('page-title', 'Edit Announcement')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.announcements.index') }}">Announcements</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-edit me-2" style="color: #cc0000;"></i>Edit Announcement</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $announcement->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="8" required>{{ old('description', $announcement->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Target Audience <span class="text-danger">*</span></label>
                        <select name="target_audience" class="form-select @error('target_audience') is-invalid @enderror" required>
                            <option value="General" {{ old('target_audience', $announcement->target_audience) == 'General' ? 'selected' : '' }}>General (All Students)</option>
                            <option value="Male" {{ old('target_audience', $announcement->target_audience) == 'Male' ? 'selected' : '' }}>Boys Only</option>
                            <option value="Female" {{ old('target_audience', $announcement->target_audience) == 'Female' ? 'selected' : '' }}>Girls Only</option>
                        </select>
                        @error('target_audience')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($announcement->attachment_path)
                    <div class="mb-4">
                        <label class="form-label">Current Attachment</label>
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                            <i class="fas fa-file fa-2x text-muted"></i>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-semibold">{{ $announcement->attachment_original_name ?? 'Attachment' }}</p>
                                <a href="{{ asset('storage/' . $announcement->attachment_path) }}" target="_blank" class="small">View File</a>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_attachment" id="removeAttachment" value="1">
                                <label class="form-check-label text-danger" for="removeAttachment">Remove</label>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-4">
                        <label class="form-label">{{ $announcement->attachment_path ? 'Replace Attachment' : 'Attachment' }} (Optional)</label>
                        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                        @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Max file size: 10MB</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Announcement
                        </button>
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Published: {{ $announcement->created_at->format('M d, Y \a\t h:i A') }}</small>
                        @if($announcement->updated_at != $announcement->created_at)
                            <br><small class="text-muted">Last updated: {{ $announcement->updated_at->format('M d, Y \a\t h:i A') }}</small>
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<strong>{{ $announcement->title }}</strong>"?</p>
                <p class="text-danger small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
