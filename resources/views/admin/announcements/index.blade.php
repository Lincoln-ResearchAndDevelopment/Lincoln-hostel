@extends('layouts.admin')

@section('page-title', 'Announcements')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Announcements</li>
            </ol>
        </nav>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0"><i class="fas fa-bullhorn me-2" style="color: #cc0000;"></i>Manage Announcements</h5>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>New Announcement
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Title</th>
                    <th>Preview</th>
                    <th>Attachment</th>
                    <th>Target</th>
                    <th>Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $announcement)
                <tr>
                    <td class="ps-3">
                        <strong>{{ $announcement->title }}</strong>
                    </td>
                    <td>{{ Str::limit(strip_tags($announcement->description), 60) }}</td>
                    <td>
                        @if($announcement->attachment_path)
                            <a href="{{ asset('storage/' . $announcement->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-paperclip me-1"></i>View
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if(($announcement->target_audience ?? 'General') == 'Male')
                            <span class="badge bg-primary">Boys Only</span>
                        @elseif(($announcement->target_audience ?? 'General') == 'Female')
                            <span class="badge bg-danger">Girls Only</span>
                        @else
                            <span class="badge bg-secondary">General</span>
                        @endif
                    </td>
                    <td>
                        {{ $announcement->created_at->format('M d, Y') }}
                        <br><small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $announcement->id }}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal{{ $announcement->id }}" tabindex="-1">
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
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">No announcements yet.</p>
                        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">Create First Announcement</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($announcements->hasPages())
    <div class="card-footer bg-white">
        {{ $announcements->links() }}
    </div>
    @endif
</div>
@endsection
