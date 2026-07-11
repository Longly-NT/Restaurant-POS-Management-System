@extends('layouts.app')

@section('title', 'Kitchen')

@section('content')
<h3 class="mb-3"><i class="bi bi-fire"></i> Kitchen / Bar Tickets</h3>

<div class="row g-4">
    <div class="col-md-4">
        <h5 class="text-uppercase text-muted small">Pending <span class="badge bg-danger">{{ $pending->count() }}</span></h5>
        @forelse($pending as $order)
            <div class="card shadow-sm mb-3 border-danger">
                <div class="card-header d-flex justify-content-between">
                    <span>{{ $order->diningTable->name }} &middot; #{{ $order->id }}</span>
                    <span class="text-muted small">{{ $order->sent_to_kitchen_at?->diffForHumans() }}</span>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item->quantity }}x {{ $item->menuItem->name }}
                                <span class="badge bg-light text-dark">{{ $item->menuItem->category->station }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
                <div class="card-body">
                    <form method="POST" action="{{ route('chef.orders.accept', $order) }}">
                        @csrf
                        <button class="btn btn-dark w-100">Accept Order</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted">No pending orders.</p>
        @endforelse
    </div>

    <div class="col-md-4">
        <h5 class="text-uppercase text-muted small">In Progress <span class="badge bg-warning text-dark">{{ $active->count() }}</span></h5>
        @forelse($active as $order)
            <div class="card shadow-sm mb-3 border-warning">
                <div class="card-header d-flex justify-content-between">
                    <span>{{ $order->diningTable->name }} &middot; #{{ $order->id }}</span>
                    <span class="badge bg-secondary text-uppercase">{{ $order->status }}</span>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item->quantity }}x {{ $item->menuItem->name }}
                                <span class="badge bg-light text-dark">{{ $item->menuItem->category->station }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
                <div class="card-body d-grid gap-2">
                    @if($order->status === 'accepted')
                        <form method="POST" action="{{ route('chef.orders.preparing', $order) }}">
                            @csrf
                            <button class="btn btn-warning w-100">Start Preparing</button>
                        </form>
                    @elseif($order->status === 'preparing')
                        <form method="POST" action="{{ route('chef.orders.finished', $order) }}">
                            @csrf
                            <button class="btn btn-success w-100">Mark Finished</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-muted">Nothing in progress.</p>
        @endforelse
    </div>

    <div class="col-md-4">
        <h5 class="text-uppercase text-muted small">Recently Finished</h5>
        @forelse($finished as $order)
            <div class="card shadow-sm mb-3 border-success">
                <div class="card-header d-flex justify-content-between">
                    <span>{{ $order->diningTable->name }} &middot; #{{ $order->id }}</span>
                    <span class="text-muted small">{{ $order->finished_at?->diffForHumans() }}</span>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($order->items as $item)
                        <li class="list-group-item">{{ $item->quantity }}x {{ $item->menuItem->name }}</li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p class="text-muted">Nothing finished yet.</p>
        @endforelse
    </div>
</div>
@endsection
