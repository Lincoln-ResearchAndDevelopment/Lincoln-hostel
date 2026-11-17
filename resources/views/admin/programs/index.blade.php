@extends('layouts.admin')
@section('page-title', 'Programs')
@section('content')
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white"><h5 class="mb-0">Programs</h5></div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.programs.store') }}" class="row g-2 mb-4">
      @csrf
      <div class="col-md-{{ $showHealth ? 4 : 6 }}"><input name="name" class="form-control" placeholder="Program name" required></div>
      <div class="col-md-3"><input name="sort_order" type="number" min="0" class="form-control" placeholder="Sort order"></div>
      @if($showHealth)
      <div class="col-md-2 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_health" value="1" id="new_is_health"><label class="form-check-label" for="new_is_health">Health course</label></div></div>
      @endif
      <div class="col-md-3"><button class="btn btn-primary w-100">Add Program</button></div>
    </form>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead><tr><th>Name</th><th>Sort</th>@if($showHealth)<th>Health course</th>@endif<th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($programs as $program)
          <tr>
            <form method="POST" action="{{ route('admin.programs.update', $program) }}">
              @csrf @method('PUT')
              <td><input name="name" class="form-control" value="{{ $program->name }}" required></td>
              <td><input name="sort_order" type="number" min="0" class="form-control" value="{{ $program->sort_order }}"></td>
              @if($showHealth)<td><input type="checkbox" name="is_health" value="1" {{ $program->is_health ? 'checked' : '' }}></td>@endif
              <td><input type="checkbox" name="is_active" value="1" {{ $program->is_active ? 'checked' : '' }}></td>
              <td class="d-flex gap-2">
                <button class="btn btn-sm btn-success">Save</button>
            </form>
            <form method="POST" action="{{ route('admin.programs.destroy', $program) }}" onsubmit="return confirm('Delete program?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
              </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">No programs found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
