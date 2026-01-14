@extends('layouts.admin')

@section('page-title', 'Hostel Rules')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Hostel Rules</li>
            </ol>
        </nav>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0"><i class="fas fa-book me-2" style="color: #cc0000;"></i>Manage Hostel Rules</h5>
    <a href="{{ route('admin.rules.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Rule
    </a>
</div>

<!-- Category Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-2">
            <span class="text-muted small me-2 align-self-center">Categories:</span>
            @foreach($categories as $key => $label)
                <span class="badge bg-secondary">{{ $label }}</span>
            @endforeach
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3" style="width: 50px;">Order</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                <tr class="{{ !$rule->is_active ? 'table-secondary' : '' }}">
                    <td class="ps-3 text-muted">{{ $rule->order }}</td>
                    <td>
                        <strong>{{ $rule->title }}</strong>
                        <br><small class="text-muted">{{ Str::limit($rule->description, 80) }}</small>
                    </td>
                    <td>
                        <span class="badge bg-{{ $rule->category == 'general' ? 'primary' : ($rule->category == 'safety' ? 'danger' : ($rule->category == 'discipline' ? 'warning' : 'info')) }}">
                            {{ $categories[$rule->category] ?? ucfirst($rule->category) }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.rules.toggle', $rule) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $rule->is_active ? 'btn-success' : 'btn-secondary' }}">
                                {{ $rule->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.rules.edit', $rule) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $rule->id }}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal{{ $rule->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Rule</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete "<strong>{{ $rule->title }}</strong>"?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('admin.rules.destroy', $rule) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">No hostel rules defined yet.</p>
                        <a href="{{ route('admin.rules.create') }}" class="btn btn-primary">Add First Rule</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($rules->hasPages())
    <div class="card-footer bg-white">
        {{ $rules->links() }}
    </div>
    @endif
</div>
@endsection
