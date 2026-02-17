@extends('layouts.student')

@section('page-title', 'Notifications')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Notifications</li>
            </ol>
        </nav>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0"><i class="fas fa-bell me-2" style="color: #cc0000;"></i>My Notifications</h5>
        @if($unreadCount > 0)
            <small class="text-muted">{{ $unreadCount }} unread notification(s)</small>
        @endif
    </div>
    @if($unreadCount > 0)
    <form action="{{ route('student.notifications.mark-all-read') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-check-double me-1"></i>Mark All as Read
        </button>
    </form>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
        <div class="list-group-item p-3 {{ $notification->isUnread() ? 'bg-light' : '' }}">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $notification->isUnread() ? 'bg-primary-subtle' : 'bg-light' }}" style="width: 45px; height: 45px;">
                        <i class="{{ $notification->icon }} {{ $notification->isUnread() ? '' : 'text-muted' }}"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 {{ $notification->isUnread() ? 'fw-bold' : '' }}">
                                {{ $notification->title }}
                                @if($notification->isUnread())
                                    <span class="badge bg-primary ms-2" style="font-size: 0.65rem;">NEW</span>
                                @endif
                            </h6>
                            <p class="mb-1 text-muted">{{ $notification->message }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                        @if($notification->isUnread())
                        <a href="{{ route('student.notifications.mark-read', $notification->id) }}" class="btn btn-sm btn-outline-secondary" title="Mark as read">
                            <i class="fas fa-check"></i>
                        </a>
                        @endif
                    </div>
                    
                    @if($notification->data)
                        @if(isset($notification->data['leave_request_id']))
                        <div class="mt-2">
                            <a href="{{ route('student.leave.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View Leave Request
                            </a>
                        </div>
                        @endif

                        @if(isset($notification->data['room_id']))
                        <div class="mt-2">
                            <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View Booking Status
                            </a>
                        </div>
                        @endif
                        
                        @if(isset($notification->data['rejection_reason']) && $notification->data['rejection_reason'])
                        <div class="mt-2 p-2 bg-danger bg-opacity-10 rounded">
                            <small class="text-danger">
                                <strong>Reason:</strong> {{ $notification->data['rejection_reason'] }}
                            </small>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="p-5 text-center">
            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No Notifications</h5>
            <p class="text-muted mb-0">You don't have any notifications yet.</p>
        </div>
        @endforelse
    </div>
    
    @if($notifications->hasPages())
    <div class="card-footer bg-white">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
