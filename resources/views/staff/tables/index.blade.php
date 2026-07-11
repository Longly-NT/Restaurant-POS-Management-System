@extends('layouts.app')

@section('title', 'Select Table')

@section('content')
<h3 class="mb-3">Select a Table</h3>

<div class="row g-3">
    @forelse($tables as $table)
        <div class="col-6 col-md-3">
            <form method="POST" action="{{ route('staff.tables.open', $table) }}">
                @csrf
                <button type="submit" class="card card-table w-100 text-center border-0 {{ $table->status == 'occupied' ? 'table-occupied' : 'table-available' }}">
                    <div class="card-body">
                        <i class="bi bi-table fs-1 {{ $table->status == 'occupied' ? 'text-danger' : 'text-success' }}"></i>
                        <h5 class="mt-2">{{ $table->name }}</h5>
                        <p class="text-muted small mb-1">Seats {{ $table->capacity }}</p>
                        <span class="badge {{ $table->status == 'occupied' ? 'bg-danger' : 'bg-success' }} text-uppercase">{{ $table->status }}</span>
                        @if($table->activeOrder)
                            <div class="small text-muted mt-2">Order #{{ $table->activeOrder->id }} &middot; {{ str_replace('_',' ', $table->activeOrder->status) }}</div>
                        @endif
                    </div>
                </button>
            </form>
        </div>
    @empty
        <p class="text-muted">No tables configured. Ask an admin to set them up.</p>
    @endforelse
</div>
@endsection
