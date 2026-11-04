@extends('layouts.app')

@section('page-title', 'Campuses')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-school me-2"></i>Campuses</h1>
            <p class="text-muted mb-0">Each campus has its own admins, hostels, rooms, applications and payment account.</p>
        </div>
        <a href="{{ route('superadmin.campuses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Campus
        </a>
    </div>

    <form method="POST" action="{{ route('superadmin.campuses.switch') }}" class="card card-body mb-3">
        @csrf
        <div class="d-flex flex-wrap align-items-center gap-2">
            <label for="switch_campus" class="fw-bold mb-0">Work in campus:</label>
            <select id="switch_campus" name="campus_id" class="form-select" style="max-width: 280px;">
                <option value="">All campuses (view only)</option>
                @foreach($campuses as $c)
                    <option value="{{ $c->id }}" {{ (int) session('active_campus_id') === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-primary">Go to admin pages</button>
        </div>
        <small class="text-muted mt-2">Pick a campus to open the admin dashboard for it. New hostels, rooms and students you add there belong to that campus.</small>
    </form>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Campus</th><th>Code</th><th>Payment account</th>
                        <th class="text-center">Hostels</th><th class="text-center">Admins</th><th>Status</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campuses as $campus)
                        <tr>
                            <td class="fw-bold">{{ $campus->name }}<div class="small text-muted">{{ $campus->address }}</div></td>
                            <td>{{ $campus->code }}</td>
                            <td>{{ $campus->bank_name }}<div class="small font-monospace">{{ $campus->account_number }} — {{ $campus->account_name }}</div></td>
                            <td class="text-center">{{ $campus->hostels_count }}</td>
                            <td class="text-center">{{ $campus->admins_count }}</td>
                            <td>
                                <span class="badge {{ $campus->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $campus->is_active ? 'Accepting applications' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('superadmin.campuses.edit', $campus) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
