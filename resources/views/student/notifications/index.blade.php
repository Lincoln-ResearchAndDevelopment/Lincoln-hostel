@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">Notification Preferences</div>
                <div class="card-body">
                    <form action="{{ url('/student/notifications') }}" method="POST">
                        @csrf
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="notifyEmail" name="email" {{ $preferences['email'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="notifyEmail">Email Notifications</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="notifySms" name="sms" {{ $preferences['sms'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="notifySms">SMS Notifications</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="notifyPush" name="push" {{ $preferences['push'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="notifyPush">Push Notifications</label>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary">Save</button>
                            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
