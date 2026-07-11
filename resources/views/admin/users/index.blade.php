@extends('layouts.app')

@section('title', 'Manage Staff')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    @if(auth()->user()->role == 'admin')
        <h3 class="mb-0">Manage Users</h3>
        <a href="{{ route('admin.users.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg"></i> Add Staff / Chef</a>
    @else
        <h3 class="mb-0">View Users</h3>
    @endif
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr><th>Name</th><th>Email</th><th>Role</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-info text-uppercase">{{ $user->role }}</span></td>
                        <td class="text-end">
                            @if(auth()->user()->role == 'admin')
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            @endif
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this user?')">
                                @csrf @method('DELETE')
                                @if(auth()->user()->role == 'admin')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No staff members yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $users->links() }}
@endsection
