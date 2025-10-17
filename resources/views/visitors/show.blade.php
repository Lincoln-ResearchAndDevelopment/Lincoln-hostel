@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Visitor Details') }}
                    <div>
                        <a href="{{ route('visitors.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Visitor Name:</strong> {{ $visitor->visitor_name }}</p>
                            <p><strong>ID Number:</strong> {{ $visitor->id_number }}</p>
                            <p><strong>Purpose:</strong> {{ $visitor->purpose }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Student:</strong> {{ $visitor->student->full_name }}</p>
                            <p><strong>Admission Number:</strong> {{ $visitor->student->admission_number }}</p>
                            <p><strong>Room:</strong> {{ $visitor->student->room ? $visitor->student->room->room_number : 'Not Assigned' }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Check-in Time:</strong> {{ $visitor->check_in_time->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Check-out Time:</strong> {{ $visitor->check_out_time ? $visitor->check_out_time->format('M d, Y H:i') : 'Still in premises' }}</p>
                        </div>
                    </div>
                    @if(!$visitor->check_out_time && Auth::user()->isAdmin())
                    <div class="row mt-3">
                        <div class="col-12">
                            <form action="{{ route('visitors.update', $visitor) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-primary">Check Out Visitor</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
