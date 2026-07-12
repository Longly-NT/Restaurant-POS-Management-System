@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
    .dash-head { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 22px; }
    .dash-head .greet { font-size: 1.5rem; font-weight: 800; letter-spacing: -.02em; margin: 0; }
    .dash-head .date { color: var(--muted); font-size: 14px; }

    /* Stat tiles */
    .stat-tile {
        background: #fff; border: 1px solid var(--border); border-radius: var(--radius);
        box-shadow: var(--shadow-sm); padding: 18px;
        display: flex; align-items: center; gap: 14px; height: 100%;
        transition: transform .12s ease, box-shadow .12s ease;
    }
    .stat-tile:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .stat-ico {
        flex-shrink: 0; width: 46px; height: 46px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background: var(--leaf-tint); color: var(--leaf-dark); font-size: 20px;
    }
    .stat-tile .label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); }
    .stat-tile .value { font-size: 1.7rem; font-weight: 800; letter-spacing: -.02em; line-height: 1.1; font-variant-numeric: tabular-nums; }

    /* Sales hero */
    .sales-hero {
        position: relative; overflow: hidden;
        border-radius: var(--radius); color: #fff;
        background:
            radial-gradient(120% 120% at 100% 0%, #2f5436 0%, transparent 55%),
            linear-gradient(150deg, var(--leaf-dark), var(--leaf-darker));
        padding: 24px 26px;
    }
    .sales-hero::after {
        content: ""; position: absolute; right: -120px; top: -120px;
        width: 320px; height: 320px; border-radius: 50%;
        border: 1px solid rgba(255,255,255,.06); box-shadow: 0 0 0 46px rgba(255,255,255,.03);
        pointer-events: none;
    }
    .sales-hero > * { position: relative; z-index: 1; }
    .sales-eyebrow { text-transform: uppercase; letter-spacing: .12em; font-size: 11px; font-weight: 700; color: var(--brass-soft, #E4CE9A); }
    .sales-net { font-size: clamp(2.2rem, 4vw, 3rem); font-weight: 800; letter-spacing: -.03em; line-height: 1; font-variant-numeric: tabular-nums; margin: 6px 0 2px; }
    .sales-net small { font-size: .9rem; font-weight: 600; opacity: .7; }
    .sales-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px 20px; margin-top: 22px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,.14); }
    .sales-grid .k { font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: rgba(255,255,255,.6); }
    .sales-grid .v { font-size: 1.15rem; font-weight: 700; font-variant-numeric: tabular-nums; letter-spacing: -.01em; }
    .sales-grid .v.neg { color: var(--brass-soft, #E4CE9A); }
    @media (max-width: 575px) { .sales-grid { grid-template-columns: repeat(2, 1fr); } }

    /* Status pills */
    .pill { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
    .pill-neutral { background: #EDEFEA; color: #5B6157; }
    .pill-cooking { background: #FBEED0; color: #8A6A17; }
    .pill-ready   { background: var(--leaf-tint-strong); color: var(--leaf-dark); }
    .pill-paid    { background: #D8EDDD; color: #2C6B3C; }
    .pill-cancel  { background: #F4DAD2; color: #9A3B23; }
</style>
@endsection

@section('content')
@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $pillClass = fn ($s) => match ($s) {
        'sent_to_kitchen', 'accepted', 'preparing' => 'pill-cooking',
        'finished', 'served' => 'pill-ready',
        'paid' => 'pill-paid',
        'cancelled' => 'pill-cancel',
        default => 'pill-neutral',
    };
@endphp

<div class="dash-head">
    <div>
        <h1 class="greet">{{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}.</h1>
        <div class="date">{{ now()->format('l, F j, Y') }}</div>
    </div>
    <a href="{{ route('staff.tables.index') }}" class="btn btn-dark btn-sm"><i class="bi bi-grid-3x3-gap"></i> Open Floor</a>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['label' => 'Staff & Chefs', 'value' => $stats['staff_count'], 'icon' => 'people', 'route' => 'admin.users.index'],
        ['label' => 'Menu Items',   'value' => $stats['menu_items'],  'icon' => 'egg-fried', 'route' => 'admin.menu-items.index'],
        ['label' => 'Tables',       'value' => $stats['tables'],      'icon' => 'grid-3x3-gap', 'route' => 'staff.tables.index'],
        ['label' => 'Active Orders','value' => $stats['active_orders'],'icon' => 'receipt', 'route' => 'admin.orders.index'],
    ] as $tile)
        <div class="col-6 col-lg-3">
            <a href="{{ route($tile['route']) }}" class="text-decoration-none">
                <div class="stat-tile">
                    <div class="stat-ico"><i class="bi bi-{{ $tile['icon'] }}"></i></div>
                    <div>
                        <div class="label">{{ $tile['label'] }}</div>
                        <div class="value">{{ $tile['value'] }}</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

{{-- Today's Sales — the focal point. Broken out, never one lump total. --}}
<div class="sales-hero mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <div class="sales-eyebrow">Today's Sales</div>
            <div class="sales-net">${{ number_format($todaySales['net_sales'], 2) }} <small>net</small></div>
            <div style="color: rgba(255,255,255,.7); font-size: 13px;">
                {{ $todaySales['transaction_count'] }} transaction{{ $todaySales['transaction_count'] === 1 ? '' : 's' }} today
            </div>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-light">View Full Reports</a>
    </div>
    <div class="sales-grid">
        <div><div class="k">Gross Sales</div><div class="v">${{ number_format($todaySales['gross_sales'], 2) }}</div></div>
        <div><div class="k">Discounts</div><div class="v neg">−${{ number_format($todaySales['discounts'], 2) }}</div></div>
        <div><div class="k">Tax Collected</div><div class="v">${{ number_format($todaySales['tax_collected'], 2) }}</div></div>
        <div><div class="k">Tips</div><div class="v">${{ number_format($todaySales['tips'], 2) }}</div></div>
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
                    <th>#</th><th>Table</th><th>Staff</th><th>Status</th><th class="text-end">Total</th><th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td class="fw-semibold">#{{ $order->id }}</td>
                        <td>{{ $order->diningTable->name }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td><span class="pill {{ $pillClass($order->status) }}">{{ str_replace('_',' ',$order->status) }}</span></td>
                        <td class="text-end fw-semibold" style="font-variant-numeric: tabular-nums;">${{ number_format($order->total, 2) }}</td>
                        <td class="text-muted small">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
