@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<h3 class="mb-4">Dashboard</h3>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Staff & Chefs</div>
                <div class="fs-2 fw-bold">{{ $stats['staff_count'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Menu Items</div>
                <div class="fs-2 fw-bold">{{ $stats['menu_items'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Tables</div>
                <div class="fs-2 fw-bold">{{ $stats['tables'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Active Orders</div>
                <div class="fs-2 fw-bold">{{ $stats['active_orders'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Recent Orders</span>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-dark">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Table</th><th>Staff</th><th>Status</th><th>Total</th><th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->diningTable->name }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td><span class="badge bg-secondary text-uppercase">{{ str_replace('_',' ',$order->status) }}</span></td>
                        <td>${{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
