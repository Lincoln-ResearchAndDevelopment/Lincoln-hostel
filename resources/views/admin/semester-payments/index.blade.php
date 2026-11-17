@extends('layouts.admin')
@section('page-title', 'Semester Payments')
@section('content')
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex flex-wrap gap-2 justify-content-between align-items-center">
    <h5 class="mb-0">Semester Payments</h5>
    <div class="btn-group btn-group-sm">
      @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'outstanding' => 'Outstanding', 'all' => 'All'] as $key => $label)
        <a href="{{ route('semester-payments.index', ['view' => $key]) }}" class="btn btn-outline-secondary {{ $view === $key ? 'active' : '' }}">{{ $label }}</a>
      @endforeach
    </div>
  </div>
  <div class="card-body">
    @if($view === 'outstanding')
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>Student</th><th>Admission No.</th><th>Program</th><th>Paid</th></tr></thead>
          <tbody>
          @forelse($students as $student)
            <tr>
              <td>{{ $student->name }}</td>
              <td>{{ $student->admission_number }}</td>
              <td>{{ $student->program }}</td>
              <td>{{ 1 + $student->semesterPayments->where('status', 'approved')->count() }} of {{ $student->semesters_to_pay }} semesters</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">No outstanding students.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    @else
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead><tr><th>Student</th><th>Semester</th><th>Service Charge Receipt</th><th>Hostel Fee Receipt</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
          @forelse($payments as $payment)
            <tr>
              <td>{{ $payment->student->name ?? '-' }}<br><small class="text-muted">{{ $payment->student->admission_number ?? '' }}</small></td>
              <td>{{ $payment->semester_number }}</td>
              <td><a href="{{ asset($payment->service_charge_receipt) }}" target="_blank">View</a></td>
              <td><a href="{{ asset($payment->hostel_fee_receipt) }}" target="_blank">View</a></td>
              <td>
                <span class="badge bg-{{ $payment->status === 'approved' ? 'success' : ($payment->status === 'rejected' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($payment->status) }}</span>
                @if($payment->admin_notes)<br><small class="text-muted">{{ $payment->admin_notes }}</small>@endif
              </td>
              <td>
                @if($payment->status === 'pending')
                <div class="d-flex gap-2">
                  <form method="POST" action="{{ route('semester-payments.approve', $payment) }}">@csrf<button class="btn btn-sm btn-success">Approve</button></form>
                  <form method="POST" action="{{ route('semester-payments.reject', $payment) }}" class="d-flex gap-1">@csrf
                    <input name="admin_notes" class="form-control form-control-sm" placeholder="Reason">
                    <button class="btn btn-sm btn-danger">Reject</button>
                  </form>
                </div>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">Nothing to show.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
      {{ $payments->links() }}
    @endif
  </div>
</div>
@endsection
