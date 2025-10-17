@extends('layouts.app')

@section('content')

<style>
.img-thumbnail {
    transition: transform 0.2s;
    object-fit: cover;
}

.img-thumbnail:hover {
    transform: scale(1.1);
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}

.modal-body iframe {
    min-height: 70vh;
    border: none;
}

.receipt-preview {
    width: 50px;
    height: 50px;
    border-radius: 4px;
    cursor: pointer;
    object-fit: cover;
    border: 1px solid #dee2e6;
}

.file-icon {
    font-size: 24px;
    margin-right: 5px;
}

.pdf-preview {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}

.pdf-preview:hover {
    background-color: #e9ecef;
}

.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border: 1px solid #dee2e6;
    color: #6c757d;
    background-color: #fff;
    border-radius: 0.25rem;
    margin: 0 2px;
    transition: all 0.15s ease-in-out;
}

.pagination .page-link:hover {
    color: #495057;
    background-color: #e9ecef;
    border-color: #dee2e6;
    text-decoration: none;
}

.pagination .page-item.active .page-link {
    background-color: #2c3e50;
    border-color: #2c3e50;
    color: #fff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

.pagination-info {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 1rem;
}
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Payments') }}
                    <a href="{{ route('payments.create') }}" class="btn" style="background-color: #2c3e50; color: white">Add New Payment</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Search Bar -->
                    <div class="mb-4">
                        <form action="{{ route('payments.index') }}" method="GET" class="row g-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Search payments by name, receipt #, amount..." value="{{ request('search') }}" aria-label="Search payments">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn w-100" style="background-color: #2c3e50; color: white"><i class="fas fa-search me-1"></i> Search</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-undo me-1"></i> Reset</a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Receipt</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->receipt_number }}</td>
                                        <td>{{ $payment->student->full_name }}</td>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                        <td>
                                            @if($payment->receipt_path)
                                                @php
                                                    // Construct the correct public path
                                                    $publicPath = 'storage/' . $payment->receipt_path;
                                                    $fullPath = public_path($publicPath);
                                                    $extension = pathinfo($payment->receipt_path, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                                    $isPdf = strtolower($extension) === 'pdf';
                                                @endphp
                                                
                                                @if(file_exists($fullPath))
                                                    @if($isImage)
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#receiptModal{{ $payment->id }}">
                                                            <img src="{{ asset($publicPath) }}" alt="Receipt" class="receipt-preview" title="View receipt">
                                                        </a>
                                                    @elseif($isPdf)
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#receiptModal{{ $payment->id }}" class="pdf-preview">
                                                            <i class="far fa-file-pdf file-icon text-danger"></i> <span>PDF</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ asset($publicPath) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="badge bg-danger">File not found</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">No receipt</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($payment->status == 'completed') bg-success 
                                                @elseif($payment->status == 'pending') bg-warning
                                                @else bg-danger @endif">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                                                <a href="{{ route('payments.show', $payment) }}" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                                <form action="{{ route('payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment?')" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="receiptModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Receipt #{{ $payment->receipt_number }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    @php
                                                        $publicPath = 'storage/' . $payment->receipt_path;
                                                        $extension = pathinfo($payment->receipt_path, PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                                        $isPdf = strtolower($extension) === 'pdf';
                                                    @endphp
                                                    
                                                    @if($isImage)
                                                        <img src="{{ asset($publicPath) }}" alt="Receipt Image" style="max-width: 100%; height: auto;">
                                                    @elseif($isPdf)
                                                        <iframe src="{{ asset($publicPath) }}" style="width: 100%; height: 500px;" frameborder="0"></iframe>
                                                    @else
                                                        <p>Unsupported file type</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <a href="{{ asset($publicPath) }}" class="btn" style="background-color: #2c3e50; color: white" download>
                                                        <i class="fas fa-download me-2"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No payments found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($payments instanceof \Illuminate\Pagination\AbstractPaginator && $payments->total() > $payments->perPage())
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                            <div class="pagination-info mb-2 mb-md-0">
                                Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $payments->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

@endsection
