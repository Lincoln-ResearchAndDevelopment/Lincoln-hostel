@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Complaint Details') }}
                    <div>
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-warning btn-sm">Edit</a>
                        @endif
                        <a href="{{ route('complaints.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Student:</strong> {{ $complaint->student->full_name }}</p>
                            <p><strong>Admission Number:</strong> {{ $complaint->student->admission_number }}</p>
                            <p><strong>Room:</strong> {{ $complaint->student->room ? $complaint->student->room->room_number : 'Not Assigned' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date Submitted:</strong> {{ $complaint->created_at->format('M d, Y H:i') }}</p>
                            <p>
                                <strong>Status:</strong>
                                <span class="badge 
                                    @if($complaint->status == 'resolved') bg-success
                                    @elseif($complaint->status == 'submitted') bg-secondary
                                    @else bg-warning @endif">
                                    {{ ucfirst($complaint->status) }}
                                </span>
                            </p>
                           @if($complaint->resolved_at)
                                <p><strong>Resolved At:</strong> 
                                    {{ is_string($complaint->resolved_at) ? \Carbon\Carbon::parse($complaint->resolved_at)->format('M d, Y H:i') : $complaint->resolved_at->format('M d, Y H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Subject:</strong> {{ $complaint->subject }}</p>
                            <p><strong>Description:</strong></p>
                            <div class="border p-3">{{ $complaint->description }}</div>
                        </div>
                    </div>
                    @if($complaint->resolution)
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Resolution:</strong></p>
                            <div class="border p-3">{{ $complaint->resolution }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
