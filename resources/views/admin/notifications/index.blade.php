@extends('layouts.admin')

@section('page-title', 'Notifications')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">All Notifications</h5>
                @if($notifications->whereNull('read_at')->count() > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">Mark all as read</button>
                </form>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                    <div class="list-group-item list-group-item-action py-3 {{ $notification->isUnread() ? 'bg-primary-subtle border-start border-primary border-4' : '' }}">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-white p-2 rounded-circle shadow-sm me-3">
                                    <i class="{{ $notification->icon }} fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $notification->title }}</h6>
                                    <p class="mb-1 text-muted small">{!! nl2br(e($notification->message)) !!}</p>
                                    <small class="text-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                @if($notification->isUnread())
                                    <a href="{{ route('admin.notifications.mark-read', $notification) }}" class="btn btn-sm btn-light border">
                                        <i class="fas fa-check me-1"></i>Mark read
                                    </a>
                                @endif
                                
                                @if(isset($notification->data['payment_id']))
                                    <a href="{{ route('payments.show', $notification->data['payment_id']) }}" class="btn btn-sm btn-primary ms-2">
                                        View Details
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No notifications found.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @if($notifications->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
