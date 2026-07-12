@extends('layouts.app')

@section('title', 'Manage Menu')


@section('content')
@if(auth()->user()->role == 'admin')
    <h3 class="mb-3">Manage Menu</h3>
@else
    <h3 class="mb-3">View Menu</h3>
@endif

<div class="row g-4">
    @if(auth()->user()->role == 'admin')
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">Add Menu Item</div>
                <div class="card-body">
                    @if($categories->isEmpty())
                        <p class="text-muted small">Create a category first.</p>
                    @else
                        <form method="POST" action="{{ route('admin.menu-items.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->station }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price ($)</label>
                                <input type="number" step="0.01" min="0" name="price" class="form-control" required>
                            </div>
                            <button class="btn btn-dark">Add Item</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Item</th><th>Category</th><th>Price</th><th>Available</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($menuItems as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->name }}</div>
                                    <div class="text-muted small">{{ $item->description }}</div>
                                </td>
                                <td>{{ $item->category->name }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>
                                    <form action="{{ route('admin.menu-items.toggle', $item) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $item->is_available ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                        </button>
                                    </form>
                                </td>
                                @if(auth()->user()->role == 'admin')
                                    <td class="text-end">
                                        <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove this item?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No menu items yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
