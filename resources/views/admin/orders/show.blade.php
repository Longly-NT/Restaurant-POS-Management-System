@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Order #{{ $order->id }} &middot; {{ $order->diningTable->name }}</h3>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">Back to orders</a>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card shadow-sm mb-3">
            <div class="card-header">Items</div>
            <div class="table-responsive" style="border-radius: var(--radius); overflow: hidden;">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->menuItem->name }}
                                    @if($item->notes)<div class="text-muted small">{{ $item->notes }}</div>@endif
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>${{ number_format($item->subtotal(), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">Payments</div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Payer</th><th>Method</th><th>Amount</th></tr>
                    </thead>
                    <tbody>
                        @forelse($order->payments as $payment)
                            <tr>
                                <td>{{ $payment->payer_label ?? '—' }}</td>
                                <td class="text-uppercase">{{ $payment->method }}</td>
                                <td>${{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No payments recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <p><strong>Status:</strong> <span class="badge bg-secondary text-uppercase">{{ str_replace('_',' ',$order->status) }}</span></p>
                <p><strong>Staff:</strong> {{ $order->user->name }}</p>
                <p><strong>Table:</strong> {{ $order->diningTable->name }}</p>
                <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
                <p><strong>Paid:</strong> ${{ number_format($order->amountPaid(), 2) }}</p>
                <p><strong>Balance due:</strong> ${{ number_format($order->balanceDue(), 2) }}</p>
                <p class="text-muted small mb-0">Created {{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
