@extends('layouts.app')

@section('title', 'Select Table')

@section('content')
<h4 class="mb-1">Select a Table</h4>
<p class="text-muted mb-4" style="font-size:14px;">Choose a table to open or resume an order.</p>

<div class="row g-3">
    @forelse($tables as $table)
        <div class="col-6 col-md-3">
            <form method="POST" action="{{ route('staff.tables.open', $table) }}">
                @csrf
                <button type="submit" class="card card-table w-100 text-center border-0 {{ $table->status == 'occupied' ? 'table-occupied' : 'table-available' }}">
                    <div class="card-body py-4">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"
                             style="width:34px;height:34px;color:{{ $table->status == 'occupied' ? 'var(--danger)' : 'var(--success)' }};">
                            <rect x="3.25" y="5" width="17.5" height="14" rx="2"/><path d="M3.25 10.5h17.5M9.5 10.5V19"/>
                        </svg>
                        <h5 class="mt-2 mb-1">{{ $table->name }}</h5>
                        <p class="text-muted small mb-2">Seats {{ $table->capacity }}</p>
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
