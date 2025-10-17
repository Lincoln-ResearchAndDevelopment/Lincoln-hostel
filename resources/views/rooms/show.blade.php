@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Room Details') }}
                    <div>
                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('rooms.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Room Number:</strong> {{ $room->room_number }}</p>
                            <p><strong>Capacity:</strong> {{ $room->capacity }}</p>
                            <p><strong>Gender Type:</strong> {{ ucfirst($room->gender_type) }}</p>
                            <p><strong>Occupied:</strong> {{ $room->occupied }}</p>
                            <p>
                                <strong>Status:</strong>
                                <span class="badge 
                                    @if($room->status == 'available') bg-success 
                                    @elseif($room->status == 'full') bg-danger
                                    @else bg-warning @endif">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </p>
                            <p><strong>Description:</strong> {{ $room->description ?: 'No description available' }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5>Students in this Room</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Admission Number</th>
                                <th>Check-in Date</th>
                                <th>Expected Check-out Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($room->students as $student)
                                <tr>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->admission_number }}</td>
                                    <td>{{ $student->check_in_date->format('M d, Y') }}</td>
                                    <td>{{ $student->expected_check_out_date->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No students in this room</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
