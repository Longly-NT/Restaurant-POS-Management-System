@extends('layouts.app')

@section('title', 'Sales Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Daily Sales Summary</h3>
    <form method="GET" class="d-flex align-items-center gap-2">
        <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="form-control form-control-sm">
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Gross Sales</div>
                <div class="fs-2 fw-bold">${{ number_format($summary['gross_sales'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Transactions</div>
                <div class="fs-2 fw-bold">{{ $summary['transaction_count'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Net Sales</div>
                <div class="fs-2 fw-bold text-success">${{ number_format($summary['net_sales'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Discounts Issued</div>
                <div class="fs-4 fw-bold text-danger">−${{ number_format($summary['discounts'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Tax Collected</div>
                <div class="fs-4 fw-bold">${{ number_format($summary['tax_collected'], 2) }}</div>
                <div class="text-muted" style="font-size:0.7rem">liability, not revenue</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Tips</div>
                <div class="fs-4 fw-bold">${{ number_format($summary['tips'], 2) }}</div>
                <div class="text-muted" style="font-size:0.7rem">payroll pass-through, not revenue</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">By Payment Method</div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr><th>Method</th><th>Count</th><th>Gross</th><th>Net</th></tr>
            </thead>
            <tbody>
                @forelse ($byPaymentMethod as $method => $data)
                    <tr>
                        <td class="text-capitalize">{{ $method }}</td>
                        <td>{{ $data['count'] }}</td>
                        <td>${{ number_format($data['gross'], 2) }}</td>
                        <td class="fw-bold">${{ number_format($data['net'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No transactions on this date.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<a href="{{ route('admin.reports.transactions', ['date' => $date]) }}" class="btn btn-outline-dark btn-sm">
    View all transactions for this date
</a>
@endsection