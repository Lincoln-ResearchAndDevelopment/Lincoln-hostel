@extends('layouts.student')

@section('page-title', 'Hostel Rules & Regulations')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Hostel Rules</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 text-white shadow" style="background: linear-gradient(135deg, #cc0000 0%, #a30000 100%);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center p-3 me-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-book fa-2x" style="color: #cc0000;"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">Hostel Rules & Regulations</h2>
                        <p class="mb-0 opacity-75">
                            Please read and adhere to all hostel rules. Violation may result in disciplinary action.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Important Notice -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <strong>Important:</strong> All residents are expected to comply with these rules. 
                Ignorance of the rules is not an excuse. Repeated violations may lead to eviction from the hostel.
            </div>
        </div>
    </div>
</div>

@if($rules->count() > 0)
    <!-- Rules by Category -->
    @foreach($rules as $category => $categoryRules)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        @switch($category)
                            @case('general')
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                @break
                            @case('safety')
                                <i class="fas fa-shield-alt text-success me-2"></i>
                                @break
                            @case('conduct')
                                <i class="fas fa-user-check text-info me-2"></i>
                                @break
                            @case('facilities')
                                <i class="fas fa-tools text-warning me-2"></i>
                                @break
                            @case('visitors')
                                <i class="fas fa-users text-secondary me-2"></i>
                                @break
                            @case('curfew')
                                <i class="fas fa-clock text-danger me-2"></i>
                                @break
                            @case('cleanliness')
                                <i class="fas fa-broom text-success me-2"></i>
                                @break
                            @case('prohibited')
                                <i class="fas fa-ban text-danger me-2"></i>
                                @break
                            @default
                                <i class="fas fa-list text-primary me-2"></i>
                        @endswitch
                        {{ $categories[$category] ?? ucfirst($category) }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordion-{{ $category }}">
                        @foreach($categoryRules as $index => $rule)
                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} bg-light" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#rule-{{ $rule->id }}">
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    {{ $rule->title }}
                                </button>
                            </h2>
                            <div id="rule-{{ $rule->id }}" 
                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                 data-bs-parent="#accordion-{{ $category }}">
                                <div class="accordion-body">
                                    {!! nl2br(e($rule->description)) !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@else
    <!-- No Rules Found -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Rules Available</h5>
                    <p class="text-muted mb-0">Hostel rules have not been published yet. Please check back later.</p>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Acknowledgment Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body p-4">
                <div class="d-flex align-items-start">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="acknowledgeRules">
                        <label class="form-check-label" for="acknowledgeRules">
                            <strong>I have read and understood all the hostel rules and regulations.</strong>
                            <br>
                            <small class="text-muted">
                                By checking this box, I acknowledge that I am aware of all the rules and agree to abide by them during my stay at the hostel.
                            </small>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Information -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-phone-alt me-2" style="color: #cc0000;"></i>Need Help?</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded p-2 me-3" style="background-color: rgba(204, 0, 0, 0.1);">
                                <i class="fas fa-user-tie" style="color: #cc0000;"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Hostel Warden</small>
                                <strong>+234 (0) 123 456 7890</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded p-2 me-3" style="background-color: rgba(204, 0, 0, 0.1);">
                                <i class="fas fa-door-open" style="color: #cc0000;"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Porter's Lodge</small>
                                <strong>+234 (0) 123 456 7891</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded p-2 me-3" style="background-color: rgba(204, 0, 0, 0.1);">
                                <i class="fas fa-ambulance" style="color: #cc0000;"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Emergency</small>
                                <strong>+234 (0) 123 456 7892</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection