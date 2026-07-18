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
                        <form method="POST" action="{{ route('admin.menu-items.store') }}" enctype="multipart/form-data">
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
                                <x-rich-text-editor name="description" id="add_description" :value="old('description')" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Allergy / Dietary Info</label>
                                <textarea name="allergy_info" class="form-control" rows="2"
                                    placeholder="e.g. Contains peanuts, dairy. Can be made gluten-free.">{{ old('allergy_info') }}</textarea>
                                <small class="text-muted">Shown as a clear warning badge to guests and kitchen staff.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price ($)</label>
                                <input type="number" step="0.01" min="0" name="price" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Supported: JPEG, PNG, GIF (Max 2MB)</small>
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
            <div class="card-header">Menu Items</div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Image</th><th>Item</th><th>Category</th><th>Price</th><th>Available</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($menuItems as $item)
                            <tr>
                                <td>
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                    @else
                                        <div style="width: 50px; height: 50px; background-color: var(--leaf-tint); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                                            <small class="text-muted">No image</small>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->name }}</div>
                                    @if($item->description)
                                        <div class="text-muted small">{!! $item->description !!}</div>
                                    @endif
                                    @if($item->allergy_info)
                                        <div class="small mt-1">
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $item->allergy_info }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $item->category->name }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>
                                    <form action="{{ route('admin.menu-items.toggle', $item) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $item->is_available ? 'btn-success' : 'btn-outline-secondary' }}">
                                            {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                        </button>
                                    </form>
                                </td>
                                @if(auth()->user()->role == 'admin')
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" style="display:inline;" data-confirm="Remove this item?">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Menu Item</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.menu-items.update', $item) }}" enctype="multipart/form-data">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Category</label>
                                                    <select name="category_id" class="form-select" required>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }} ({{ $category->station }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <x-rich-text-editor name="description" id="edit_description_{{ $item->id }}" :value="$item->description" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Allergy / Dietary Info</label>
                                                    <textarea name="allergy_info" class="form-control" rows="2"
                                                        placeholder="e.g. Contains peanuts, dairy. Can be made gluten-free.">{{ $item->allergy_info }}</textarea>
                                                    <small class="text-muted">Shown as a clear warning badge to guests and kitchen staff.</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Price ($)</label>
                                                    <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ $item->price }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Image</label>
                                                    @if($item->image)
                                                        <div class="mb-2">
                                                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                    <small class="text-muted">Supported: JPEG, PNG, GIF (Max 2MB)</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                
                                                <button type="submit" class="btn btn-primary">Update Item</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No menu items yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
