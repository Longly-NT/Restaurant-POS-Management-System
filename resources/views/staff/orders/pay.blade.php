@extends('layouts.app')

@section('title', 'Bill - Order #' . $order->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Bill &middot; {{ $order->diningTable->name }} &middot; Order #{{ $order->id }}</h3>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#receiptModal">
                <i class="bi bi-printer"></i> Print Receipt
            </button>
            <a href="{{ route('staff.orders.show', $order) }}" class="btn btn-outline-secondary btn-sm">Back to Order</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">Order Summary</div>
                <div class="table-responsive" style="border-radius: var(--radius); overflow: hidden;">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
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
                        @php
                            $receiptDiscount = $order->payments->sum('discount_amount');
                            $receiptTax = $order->payments->sum('tax_amount');
                            $receiptTip = $order->payments->sum('tip_amount');
                            $receiptTotal = $order->total + $receiptTax + $receiptTip - $receiptDiscount;
                        @endphp
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th>${{ number_format($receiptTotal, 2) }}</th>
                            </tr>
                            @php
                                $receiving = round($order->payments->sum('tendered_amount'), 2);
                                $changeDue = $receiving - round($receiptTotal, 2);
                            @endphp
                            @if($changeDue > 0)
                                <tr class="table-success">
                                    <th colspan="2">Change Due</th>
                                    <th>${{ number_format($changeDue, 2) }}</th>
                                </tr>
                            @endif
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
                                <span>Payment <span
                                        class="badge bg-light text-dark text-uppercase">{{ $payment->payment_method }}</span></span>
                                <span class="fw-bold">${{ number_format($payment->totalCollected(), 2) }}</span>
                            </div>
                            <div class="text-muted small mt-1">
                                Base ${{ number_format($payment->subtotal_amount, 2) }}
                                @if($payment->tax_amount > 0) &middot; Tax ${{ number_format($payment->tax_amount, 2) }} @endif
                                @if($payment->tip_amount > 0) &middot; Tip ${{ number_format($payment->tip_amount, 2) }} @endif
                                @if($payment->discount_amount > 0)
                                    &middot; <span class="text-danger">Discount
                                        {{ rtrim(rtrim(number_format($payment->discount_percent, 2), '0'), '.') }}%
                                        (−${{ number_format($payment->discount_amount, 2) }})</span>
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
                                <label class="form-label">Receiving Amount ($)</label>
                                <input type="number" step="0.01" min="{{ $order->balanceDue() }}" name="amount"
                                    id="amountInput" class="form-control" value="{{ old('amount', $order->balanceDue()) }}"
                                    required>
                                <div class="form-text">Enter an amount equal to or greater than the remaining balance due.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tip ($, optional)</label>
                                <input type="number" step="0.01" min="0" name="tip_amount" id="tipInput" class="form-control"
                                    value="{{ old('tip_amount', 0) }}">
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="discountToggle"
                                    onchange="document.getElementById('discountFields').classList.toggle('d-none', !this.checked)">
                                <label class="form-check-label" for="discountToggle">Apply a discount</label>
                            </div>

                            <div id="discountFields" class="d-none border rounded p-3 mb-3 bg-light">
                                <div class="mb-2">
                                    <label class="form-label">Discount (%) From the original price </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="100" name="discount_percent"
                                            id="discountInput" class="form-control"
                                            value="{{ old('discount_percent', 0) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted" id="discountAmountPreview"></small>
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

                            <div class="alert alert-secondary d-flex justify-content-between mb-3" id="estimatePreview">
                                <span>Estimated total to collect</span>
                                <span class="fw-bold" id="totalPreview">$0.00</span>
                            </div>

                            <button class="btn btn-dark w-100">Record Payment</button>
                        </form>
                    </div>
                </div>


            @else
                <div class="alert alert-success">This order is fully paid. Table is now free.</div>
            @endif
        </div>
    </div>

    {{-- Print Receipt Modal --}}
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-print-none">
                    <h5 class="modal-title" id="receiptModalLabel">Receipt Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="receiptPrintArea" class="receipt-print-area mx-auto">
                        <div class="text-center mb-3">
                            <h5 class="mb-0">Angkor Khmer Cuisine</h5>
                            <div class="small text-muted">Order Receipt</div>
                        </div>
                        <div class="small mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Order #</span>
                                <span>{{ $order->id }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Table</span>
                                <span>{{ $order->diningTable->name }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Date</span>
                                <span>{{ $order->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            @if($order->user)
                                <div class="d-flex justify-content-between">
                                    <span>Served by</span>
                                    <span>{{ $order->user->name }}</span>
                                </div>
                            @endif
                        </div>

                        <hr class="my-2">

                        <table class="table table-sm mb-2">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->menuItem->name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->subtotal(), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <hr class="my-2">

                        @php
                            $receiptDiscount = $order->payments->sum('discount_amount');
                            $receiptTax = $order->payments->sum('tax_amount');
                            $receiptTip = $order->payments->sum('tip_amount');
                        @endphp

                        <div class="small">
                            <div class="d-flex justify-content-between">
                                <span>Total</span>
                                <span>${{ number_format($receiptTotal, 2) }}</span>
                            </div>
                            @if($receiptDiscount > 0)
                                <div class="d-flex justify-content-between">
                                    <span>Discount</span>
                                    <span class="text-danger">- ${{ number_format($receiptDiscount, 2) }}</span>
                                </div>
                            @endif
                            @if($receiptTax > 0)
                                <div class="d-flex justify-content-between">
                                    <span>Tax</span>
                                    <span>${{ number_format($receiptTax, 2) }}</span>
                                </div>
                            @endif
                            @if($receiptTip > 0)
                                <div class="d-flex justify-content-between">
                                    <span>Tip</span>
                                    <span>${{ number_format($receiptTip, 2) }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between">
                                <span>Receiving Amount</span>
                                <span>${{ number_format($order->payments->sum('tendered_amount'), 2) }}</span>
                            </div>
                            @php
                                $receiving = round($order->payments->sum('tendered_amount'), 2);
                                $changeDue = $receiving - round($receiptTotal, 2);
                            @endphp
                            @if($changeDue > 0)
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Change Due</span>
                                    <span>${{ number_format($changeDue, 2) }}</span>
                                </div>
                            @endif
                        </div>

                        @if($order->payments->isNotEmpty())
                            <hr class="my-2">
                            <div class="small">
                                <div class="fw-bold mb-1">Payments</div>
                                @foreach($order->payments as $payment)
                                    <div class="d-flex justify-content-between">
                                        <span class="text-uppercase">{{ $payment->payment_method }}</span>
                                        <span>${{ number_format($payment->totalCollected(), 2) }}</span>
                                    </div>
                                    @if($payment->discount_amount > 0 || $payment->tax_amount > 0 || $payment->tip_amount > 0)
                                        <div class="small text-muted ms-3 mt-1">
                                            @if($payment->discount_amount > 0)
                                                <div>Discount -${{ number_format($payment->discount_amount, 2) }} ({{ rtrim(rtrim(number_format($payment->discount_percent, 2), '0'), '.') }}%)</div>
                                            @endif
                                            @if($payment->tax_amount > 0)
                                                <div>Tax ${{ number_format($payment->tax_amount, 2) }}</div>
                                            @endif
                                            @if($payment->tip_amount > 0)
                                                <div>Tip ${{ number_format($payment->tip_amount, 2) }}</div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <hr class="my-2">
                        <div class="text-center small text-muted">Thank you for dining with us!</div>
                    </div>
                </div>
                <div class="modal-footer d-print-none">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-dark" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #receiptPrintArea, #receiptPrintArea * {
                visibility: visible;
            }
            /* Put receipt at the top/center of the printed page and neutralize
               any modal transforms that would offset it when printing. */
            .modal, .modal-dialog, .modal-content {
                position: static !important;
                transform: none !important;
                box-shadow: none !important;
            }
            #receiptPrintArea {
                position: fixed !important;
                left: 50% !important;
                top: 0 !important;
                transform: translateX(-50%) !important;
                width: 380px !important;
                margin: 0 !important;
                padding-top: 4mm;
            }
            @page {
                margin: 0.5cm;
            }
        }
        .receipt-print-area {
            max-width: 380px;
            font-family: 'Courier New', monospace;
        }
    </style>
@endsection

@section('scripts')
    <script>
        const orderTotal = parseFloat({{ $order->total }});
        const taxRate = {{ config('pos.tax_rate') }};

        function updatePreview() {
            const tip = parseFloat(document.getElementById('tipInput')?.value) || 0;
            const discountPercent = Math.min(parseFloat(document.getElementById('discountInput')?.value) || 0, 100);
            const discount = parseFloat((orderTotal * (discountPercent / 100)).toFixed(2));
            const taxable = Math.max(orderTotal - discount, 0);
            const tax = parseFloat((taxable * taxRate).toFixed(2));
            const total = parseFloat((taxable + tax + tip).toFixed(2));

            const discountPreviewEl = document.getElementById('discountAmountPreview');
            if (discountPreviewEl) {
                discountPreviewEl.textContent = discount > 0 ? ('= -$' + discount.toFixed(2) + ' off') : '';
            }

            const totalPreviewEl = document.getElementById('totalPreview');
            if (totalPreviewEl) {
                totalPreviewEl.textContent = '$' + total.toFixed(2);
            }
        }

        ['tipInput', 'discountInput'].forEach(id => {
            document.getElementById(id)?.addEventListener('input', updatePreview);
        });
        updatePreview();
    </script>
@endsection