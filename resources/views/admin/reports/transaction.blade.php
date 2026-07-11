@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<h3 class="mb-4">Transactions</h3>

<form method="GET" class="d-flex align-items-center gap-2 mb-4">
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm" style="width:auto">
    <span class="text-muted small">to</span>
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm" style="width:auto">
    <button class="btn btn-sm btn-dark">Filter</button>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Txn ID</th>
                    <th>Date</th>
                    <th>Table</th>
                    <th>Amount</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Tip</th>
                    <th>Total Collected</th>
                    <th>Method</th>
                    <th>Payer</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $txn)
                    <tr>
                        <td>#{{ $txn->id }}</td>
                        <td>{{ $txn->created_at->format('M j, Y g:ia') }}</td>
                        <td>{{ $txn->order->diningTable->name ?? '—' }}</td>
                        <td>${{ number_format($txn->subtotal_amount, 2) }}</td>
                        <td class="{{ $txn->discount_amount > 0 ? 'text-danger' : 'text-muted' }}">
                            @if($txn->discount_amount > 0)
                                −${{ number_format($txn->discount_amount, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td>${{ number_format($txn->tax_amount, 2) }}</td>
                        <td>${{ number_format($txn->tip_amount, 2) }}</td>
                        <td class="fw-bold">${{ number_format($txn->total_amount, 2) }}</td>
                        <td class="text-capitalize">{{ $txn->payment_method }}</td>
                        <td>{{ $txn->processedBy->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $transactions->links() }}</div>
@endsection