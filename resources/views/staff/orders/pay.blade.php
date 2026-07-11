@extends('layouts.app')

@section('title', 'Bill - Order #' . $order->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Bill &middot; {{ $order->diningTable->name }} &middot; Order #{{ $order->id }}</h3>
    <a href="{{ route('staff.orders.show', $order) }}" class="btn btn-outline-secondary btn-sm">Back to Order</a>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-header">Order Summary</div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Item</th><th>Qty</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->menuItem->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->subtotal(), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><th colspan="2">Total</th><th>${{ number_format($order->total, 2) }}</th></tr>
                        <tr><th colspan="2">Paid</th><th>${{ number_format($order->amountPaid(), 2) }}</th></tr>
                        <tr class="table-warning"><th colspan="2">Balance Due</th><th>${{ number_format($order->balanceDue(), 2) }}</th></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">Payments Recorded</div>
            <ul class="list-group list-group-flush">
                @forelse($order->payments as $payment)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span>{{ $payment->payer_label ?? 'Payment' }} <span class="badge bg-light text-dark text-uppercase">{{ $payment->method }}</span></span>
                            <span class="fw-bold">${{ number_format($payment->totalCollected(), 2) }}</span>
                        </div>
                        <div class="text-muted small mt-1">
                            Base ${{ number_format($payment->amount, 2) }}
                            @if($payment->tax_amount > 0) &middot; Tax ${{ number_format($payment->tax_amount, 2) }} @endif
                            @if($payment->tip_amount > 0) &middot; Tip ${{ number_format($payment->tip_amount, 2) }} @endif
                            @if($payment->discount_amount > 0)
                                &middot; <span class="text-danger">Discount −${{ number_format($payment->discount_amount, 2) }}</span>
                                ({{ $payment->discount_reason }}, approved by {{ $payment->discountAuthorizedBy->name ?? '—' }})
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No payments yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-md-6">
        @if($order->balanceDue() > 0)
            <div class="card shadow-sm mb-3">
                <div class="card-header">Record a Payment</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('staff.orders.payments.store', $order) }}" id="paymentForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Payer label (optional)</label>
                            <input type="text" name="payer_label" class="form-control" placeholder="e.g. Guest 1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount ($)</label>
                            <input type="number" step="0.01" min="0.01" max="{{ $order->balanceDue() }}"
                                   name="amount" id="amountInput" class="form-control"
                                   value="{{ old('amount', $order->balanceDue()) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tip ($, optional)</label>
                            <input type="number" step="0.01" min="0" name="tip_amount" id="tipInput"
                                   class="form-control" value="{{ old('tip_amount', 0) }}">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="discountToggle"
                                   onchange="document.getElementById('discountFields').classList.toggle('d-none', !this.checked)">
                            <label class="form-check-label" for="discountToggle">Apply a discount</label>
                        </div>

                        <div id="discountFields" class="d-none border rounded p-3 mb-3 bg-light">
                            <div class="mb-2">
                                <label class="form-label">Discount amount ($)</label>
                                <input type="number" step="0.01" min="0" name="discount_amount" id="discountInput"
                                       class="form-control" value="{{ old('discount_amount', 0) }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Reason</label>
                                <input type="text" name="discount_reason" class="form-control"
                                       placeholder="e.g. comped drink, service issue" value="{{ old('discount_reason') }}">
                            </div>
                           
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Method</label>
                            <select name="method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mobile">Mobile</option>
                            </select>
                        </div>

                        {{-- Live preview only — the real tax is always recalculated server-side --}}
                        <div class="alert alert-secondary d-flex justify-content-between mb-3">
                            <span>Estimated total to collect</span>
                            <span class="fw-bold" id="totalPreview">$0.00</span>
                        </div>

                        <button class="btn btn-dark w-100">Record Payment</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">Split Bill Evenly</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('staff.orders.split', $order) }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">Number of guests</label>
                                <input type="number" name="splits" min="2" max="20" value="2" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Method</label>
                                <select name="method" class="form-select" required>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="mobile">Mobile</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-outline-dark w-100 mt-3">Split & Mark Paid</button>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-success">This order is fully paid. Table is now free.</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    const taxRate = {{ config('pos.tax_rate') }};

    function updatePreview() {
        const amount = parseFloat(document.getElementById('amountInput').value) || 0;
        const tip = parseFloat(document.getElementById('tipInput').value) || 0;
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const taxable = Math.max(amount - discount, 0);
        const tax = taxable * taxRate;
        const total = amount - discount + tax + tip;
        document.getElementById('totalPreview').textContent = '$' + total.toFixed(2);
    }

    ['amountInput', 'tipInput', 'discountInput'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', updatePreview);
    });
    updatePreview();
</script>
@endsection