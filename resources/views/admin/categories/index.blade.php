@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')
@if(auth()->user()->role == 'admin')
    <h3 class="mb-3">Manage Categories</h3>
@else
    <h3 class="mb-3">View Categories</h3>
@endif

<div class="row g-4">
    @if(auth()->user()->role == 'admin')
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header">Add Category</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Station</label>
                            <select name="station" class="form-select" required>
                                <option value="kitchen">Kitchen</option>
                                <option value="bar">Bar</option>
                            </select>
                        </div>
                        <button class="btn btn-dark">Add Category</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Name</th><th>Station</th><th>Items</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    @if(auth()->user()->role == 'admin')
                                        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="d-flex gap-2 align-items-center">
                                            @csrf @method('PUT')
                                            <input type="text" name="name" value="{{ $category->name }}" class="form-control form-control-sm" style="max-width:160px">
                                            <select name="station" class="form-select form-select-sm" style="max-width:110px">
                                                <option value="kitchen" {{ $category->station == 'kitchen' ? 'selected' : '' }}>Kitchen</option>
                                                <option value="bar" {{ $category->station == 'bar' ? 'selected' : '' }}>Bar</option>
                                            </select>
                                            <button class="btn btn-sm btn-outline-secondary">Save</button>
                                        </form>
                                    @else
                                        {{ $category->name}}
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary text-uppercase">{{ $category->station }}</span></td>
                                <td>{{ $category->menu_items_count }}</td>

                                @if(auth()->user()->role == 'admin')
                                    <td class="text-end">
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" data-confirm="Delete category and its menu items?">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No categories yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
