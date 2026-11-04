@extends('layouts.app')

@section('page-title', $campus->exists ? 'Edit Campus' : 'Add Campus')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-school me-2"></i>{{ $campus->exists ? 'Edit Campus' : 'Add Campus' }}</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ $campus->exists ? route('superadmin.campuses.update', $campus) : route('superadmin.campuses.store') }}">
                        @csrf
                        @if($campus->exists) @method('PUT') @endif

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label" for="name">Campus name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $campus->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="code">Code <span class="text-danger">*</span></label>
                                <input type="text" id="code" name="code" maxlength="10" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code', $campus->code) }}" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="address">Address</label>
                            <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $campus->address) }}">
                        </div>

                        <h5 class="mt-4 mb-3">Payment account (shown to applicants of this campus)</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="bank_name">Bank <span class="text-danger">*</span></label>
                                <input type="text" id="bank_name" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $campus->bank_name) }}" required>
                                @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="account_number">Account number <span class="text-danger">*</span></label>
                                <input type="text" id="account_number" name="account_number" class="form-control font-monospace @error('account_number') is-invalid @enderror" value="{{ old('account_number', $campus->account_number) }}" required>
                                @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="account_name">Account name <span class="text-danger">*</span></label>
                                <input type="text" id="account_name" name="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name', $campus->account_name) }}" required>
                                @error('account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $campus->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Accepting applications</label>
                        </div>

                        <a href="{{ route('superadmin.campuses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save campus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
