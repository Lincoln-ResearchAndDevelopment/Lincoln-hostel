@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Admin Dashboard') }}</div>

                <div class="card-body">
                    <div class="row">
                        <!-- Rooms Summary -->
                        <div class="col-md-3 mb-4">
                            <div class="card text-white" style="background-color: #2c3e50; border-radius: 20px;">
                                <div class="card-body">
                                    <h5 class="card-title">Rooms</h5>
                                    <p class="card-text display-4">{{ $total_rooms }}</p>
                                    <p>Available: {{ $available_rooms }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Students Summary -->
                        <div class="col-md-3 mb-4">
                            <div class="card text-white" style="background-color: #2c3e50; border-radius: 20px;">
                                <div class="card-body">
                                    <h5 class="card-title">Students</h5>
                                    <p class="card-text display-4">{{ $total_students }}</p>
                                    <p>Active residents</p>
                                </div>
                            </div>
                        </div>

                        <!-- Complaints Summary -->
                        <div class="col-md-3 mb-4">
                            <div class="card text-white" style="background-color: #2c3e50; border-radius: 20px;">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Complaints</h5>
                                    <p class="card-text display-4">{{ $pending_complaints }}</p>
                                    <p>Require attention</p>
                                </div>
                            </div>
                        </div>

                        <!-- Visitors Summary -->
                        <div class="col-md-3 mb-4">
                            <div class="card text-white" style="background-color: #2c3e50; border-radius: 20px;">
                                <div class="card-body">
                                    <h5 class="card-title">Current Visitors</h5>
                                    <p class="card-text display-4">{{ $recent_visitors->count() }}</p>
                                    <p>In the premises</p>
                                </div>
                            </div>
                        </div>

                        <!-- Announcements Box -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Send Announcements</span>
                                    <button type="button" class="btn" style="background-color:#2c3e50; color: white;" data-bs-toggle="modal" data-bs-target="#announcementsModal">
                                        View Recent Announcements
                                    </button>
                                </div>

                                <div style="margin-top: 20px; margin-left: 20px; margin-right: 20px;">
                                    @if(session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif
                                </div>

                                <form action="{{ route('announcements.store') }}" method="POST" style="margin-left: 20px; margin-right: 20px; margin-bottom: 20px;" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="title" class="form-label"><h5>Subject</h5></label>
                                        <input type="text" name="title" id="title" class="form-control" required value="{{ old('title') }}">
                                        @error('title')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label"><h5>Description</h5></label>
                                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                        @error('description')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="attachment" class="form-label"><h5>Attachment (Optional)</h5></label>
                                        <input type="file" name="attachment" id="attachment" class="form-control">
                                        <small class="text-muted">Upload images, PDFs, Word documents, etc. (Max: 10MB)</small>
                                        @error('attachment')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn" style="background-color: #2c3e50; color: white;">Send Announcement</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Announcements Modal -->
                        <div class="modal fade" id="announcementsModal" tabindex="-1" aria-labelledby="announcementsModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="announcementsModalLabel">Recent Announcements</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                            <div style="margin-top: 20px; margin-left: 20px; margin-right: 20px;">
                                                @if(session('success'))
                                                    <div class="alert alert-success">{{ session('success') }}</div>
                                                @endif
                                            </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="15%">Subject</th>
                                                        <th width="40%">Description</th>
                                                        <th width="15%">Attachment</th>
                                                        <th width="25%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($allAnnouncements as $index => $announcement)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $announcement->title }}</td>
                                                            <td>{{ Str::limit($announcement->description, 100) }}</td>
                                                            <td>
                                                                @if($announcement->hasAttachment())
                                                                    <a href="{{ route('announcements.download', $announcement) }}" class="btn btn-sm btn-outline-secondary">
                                                                        <i class="{{ $announcement->getAttachmentTypeIcon() }}"></i>
                                                                        {{ Str::limit($announcement->attachment_original_name, 15) }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">None</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-primary mb-1" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#editAnnouncementModal"
                                                                        data-id="{{ $announcement->id }}"
                                                                        data-title="{{ $announcement->title }}"
                                                                        data-description="{{ $announcement->description }}"
                                                                        data-has-attachment="{{ $announcement->hasAttachment() ? '1' : '0' }}"
                                                                        data-attachment-name="{{ $announcement->attachment_original_name }}">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger mb-1" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#deleteAnnouncementModal"
                                                                        data-id="{{ $announcement->id }}"
                                                                        data-title="{{ $announcement->title }}">
                                                                    <i class="fas fa-trash-alt"></i> Delete
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No announcements found</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Announcement Modal -->
                        <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Announcement</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form id="editAnnouncementForm" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="edit_title" class="form-label">Subject</label>
                                                <input type="text" name="title" id="edit_title" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_description" class="form-label">Description</label>
                                                <textarea name="description" id="edit_description" class="form-control" rows="5"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_attachment" class="form-label">Attachment</label>
                                                <input type="file" name="attachment" id="edit_attachment" class="form-control">
                                                <small class="text-muted">Upload a new file to replace the existing one (Max: 10MB)</small>
                                            </div>
                                            <div class="mb-3" id="current_attachment_container" style="display: none;">
                                                <label class="form-label">Current Attachment</label>
                                                <div class="d-flex align-items-center">
                                                    <span id="current_attachment_name" class="me-2"></span>
                                                    <div class="form-check ms-3">
                                                        <input class="form-check-input" type="checkbox" name="remove_attachment" id="remove_attachment">
                                                        <label class="form-check-label" for="remove_attachment">
                                                            Remove attachment
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Announcement Modal -->
                        <div class="modal fade" id="deleteAnnouncementModal" tabindex="-1" aria-labelledby="deleteAnnouncementModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form id="deleteAnnouncementForm" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteAnnouncementModalLabel">Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete this announcement?</p>
                                            <p><strong id="deleteAnnouncementTitle"></strong></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Recent Payments -->
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">Recent Payments</div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recent_payments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->student->full_name }}</td>
                                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                                        <td>
                                                            <span class="badge 
                                                                @if($payment->status == 'completed') bg-success 
                                                                @elseif($payment->status == 'pending') bg-warning
                                                                @else bg-danger @endif">
                                                                {{ ucfirst($payment->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">No recent payments</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Visitors -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">Current Visitors</div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Visitor</th>
                                                    <th>Visiting</th>
                                                    <th>Check In</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recent_visitors as $visitor)
                                                    <tr>
                                                        <td>{{ $visitor->visitor_name }}</td>
                                                        <td>{{ $visitor->student->full_name }}</td>
                                                        <td>{{ $visitor->check_in_time->format('M d, Y H:i') }}</td>
                                                        <td>
                                                            <form action="{{ route('visitors.update', $visitor) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm" style="background-color: #2c3e50; color: white">Check Out</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">No current visitors</td>
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
            </div>
        </div>
    </div>
</div>

<script>
    // Edit Announcement Modal Handler
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('editAnnouncementModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const description = button.getAttribute('data-description');
            const hasAttachment = button.getAttribute('data-has-attachment') === '1';
            const attachmentName = button.getAttribute('data-attachment-name');
            
            const form = editModal.querySelector('form');
            form.action = `/announcements/${id}`;
            
            editModal.querySelector('#edit_title').value = title;
            editModal.querySelector('#edit_description').value = description;
            
            // Handle attachment display
            const currentAttachmentContainer = document.getElementById('current_attachment_container');
            const currentAttachmentName = document.getElementById('current_attachment_name');
            const removeAttachmentCheckbox = document.getElementById('remove_attachment');
            
            if (hasAttachment) {
                currentAttachmentContainer.style.display = 'block';
                currentAttachmentName.textContent = attachmentName;
                removeAttachmentCheckbox.checked = false;
            } else {
                currentAttachmentContainer.style.display = 'none';
            }
            
            // Reset file input
            document.getElementById('edit_attachment').value = '';
        });
    });
</script>

<script>
    // Delete Announcement Modal Handler
    document.addEventListener('DOMContentLoaded', function() {
        var deleteModal = document.getElementById('deleteAnnouncementModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var title = button.getAttribute('data-title');

            var form = deleteModal.querySelector('form');
            form.action = '/announcements/' + id;

            var titleElement = deleteModal.querySelector('#deleteAnnouncementTitle');
            titleElement.textContent = title;
        });
    });
</script>

@endsection