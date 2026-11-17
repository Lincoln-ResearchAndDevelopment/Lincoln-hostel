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

                        <h5 class="mt-4 mb-3">Hostel fee account (shown to applicants of this campus)</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="bank_name">Bank</label>
                                <input type="text" id="bank_name" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $campus->bank_name) }}">
                                @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="account_number">Account number</label>
                                <input type="text" id="account_number" name="account_number" class="form-control font-monospace @error('account_number') is-invalid @enderror" value="{{ old('account_number', $campus->account_number) }}">
                                @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="account_name">Account name</label>
                                <input type="text" id="account_name" name="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name', $campus->account_name) }}">
                                @error('account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <h5 class="mt-4 mb-1">Payments made outside this system</h5>
                        <div class="form-check mb-3">
                            <input type="hidden" name="payments_external" value="0">
                            <input class="form-check-input" type="checkbox" id="payments_external" name="payments_external" value="1" {{ old('payments_external', $campus->payments_external) ? 'checked' : '' }}>
                            <label class="form-check-label" for="payments_external">Students pay outside this system</label>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="health_semesters">Hostel fee semesters, health courses</label>
                                <input type="number" min="1" max="12" id="health_semesters" name="health_semesters" class="form-control @error('health_semesters') is-invalid @enderror" value="{{ old('health_semesters', $campus->health_semesters ?? 2) }}">
                                @error('health_semesters')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="non_health_semesters">Hostel fee semesters, other courses</label>
                                <input type="number" min="1" max="12" id="non_health_semesters" name="non_health_semesters" class="form-control @error('non_health_semesters') is-invalid @enderror" value="{{ old('non_health_semesters', $campus->non_health_semesters ?? 3) }}">
                                @error('non_health_semesters')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <h5 class="mt-4 mb-1">Service charge</h5>
                        <p class="text-muted small mb-3">Applicants pay this separately from the hostel fee, into the account below, and upload its receipt. Set it <strong>per semester</strong>, <strong>per year</strong>, or both: students pay the one that matches the payment plan they choose.</p>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="form_fee_amount">Application form fee, one-time (₦)</label>
                                <input type="number" step="0.01" min="0" id="form_fee_amount" name="form_fee_amount" class="form-control @error('form_fee_amount') is-invalid @enderror" value="{{ old('form_fee_amount', $campus->exists ? $campus->form_fee_amount : 2000) }}">
                                @error('form_fee_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="service_charge_amount">Per semester (₦)</label>
                                <input type="number" step="0.01" min="0" id="service_charge_amount" name="service_charge_amount" class="form-control @error('service_charge_amount') is-invalid @enderror" value="{{ old('service_charge_amount', $campus->service_charge_amount) }}">
                                @error('service_charge_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="service_charge_amount_year">Per year (₦)</label>
                                <input type="number" step="0.01" min="0" id="service_charge_amount_year" name="service_charge_amount_year" class="form-control @error('service_charge_amount_year') is-invalid @enderror" value="{{ old('service_charge_amount_year', $campus->service_charge_amount_year) }}">
                                @error('service_charge_amount_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Fill in at least one of the two amounts.</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="service_charge_bank_name">Bank</label>
                                <input type="text" id="service_charge_bank_name" name="service_charge_bank_name" class="form-control @error('service_charge_bank_name') is-invalid @enderror" value="{{ old('service_charge_bank_name', $campus->service_charge_bank_name) }}">
                                @error('service_charge_bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="service_charge_account_number">Account number</label>
                                <input type="text" id="service_charge_account_number" name="service_charge_account_number" class="form-control font-monospace @error('service_charge_account_number') is-invalid @enderror" value="{{ old('service_charge_account_number', $campus->service_charge_account_number) }}">
                                @error('service_charge_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="service_charge_account_name">Account name</label>
                                <input type="text" id="service_charge_account_name" name="service_charge_account_name" class="form-control @error('service_charge_account_name') is-invalid @enderror" value="{{ old('service_charge_account_name', $campus->service_charge_account_name) }}">
                                @error('service_charge_account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
