@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('styles')
<style>
    .menu-scroll { max-height: 66vh; overflow-y: auto; padding-right: 4px; }
    .cat-label {
        display: flex; align-items: center; gap: 8px;
        font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
        color: var(--muted); margin: 18px 0 10px;
    }
    .cat-label::after { content: ""; flex: 1; height: 1px; background: var(--border); }

    .dish {
        border: 1px solid var(--border); border-radius: var(--radius);
        background: #fff; overflow: hidden; height: 100%;
        display: flex; flex-direction: column;
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }
    .dish:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: var(--leaf-tint-strong); }
    .dish-media { position: relative; height: 132px; background: var(--leaf-tint); }
    .dish-media img { width: 100%; height: 100%; object-fit: cover; }
    .dish-media .noimg {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        color: var(--leaf-dark); opacity: .4;
    }
    .dish-price {
        position: absolute; top: 8px; right: 8px;
        background: rgba(31,57,36,.92); color: #fff;
        font-weight: 700; font-size: 12.5px; letter-spacing: -.01em;
        padding: 3px 9px; border-radius: 999px;
        font-variant-numeric: tabular-nums;
    }
    .dish-body { padding: 10px 12px 12px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
    .dish-name { font-weight: 700; font-size: 14px; line-height: 1.25; }
    .dish-desc { color: var(--muted); font-size: 12px; line-height: 1.4; margin-top: 2px; }

    /* Quantity stepper */
    .stepper { display: flex; align-items: center; margin-top: auto; gap: 8px; }
    .qty { display: inline-flex; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; }
    .qty button {
        width: 30px; border: 0; background: #fff; color: var(--ink);
        font-size: 16px; line-height: 1; cursor: pointer;
    }
    .qty button:hover { background: var(--leaf-tint); }
    .qty input {
        width: 38px; border: 0; text-align: center; font-weight: 600;
        -moz-appearance: textfield; font-variant-numeric: tabular-nums;
    }
    .qty input::-webkit-outer-spin-button, .qty input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    /* Order ticket */
    .ticket-line { display: flex; align-items: flex-start; gap: 10px; padding: 12px 0; border-top: 1px solid var(--border); }
    .ticket-line:first-child { border-top: 0; }
    .ticket-qty {
        flex-shrink: 0; min-width: 30px; height: 30px; padding: 0 6px;
        border-radius: 8px; background: var(--leaf-tint); color: var(--leaf-dark);
        font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center;
        font-variant-numeric: tabular-nums;
    }
    .ticket-name { font-weight: 600; font-size: 14px; }
    .ticket-note { color: var(--muted); font-size: 12px; }
    .ticket-sub { font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .ticket-total { display: flex; justify-content: space-between; align-items: baseline; padding-top: 14px; border-top: 2px solid var(--ink); margin-top: 4px; }
    .ticket-total .amt { font-size: 1.5rem; font-weight: 800; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .btn-icon { border: 1px solid var(--border); background:#fff; color: var(--muted); width: 30px; height: 30px; border-radius: 8px; line-height: 1; }
    .btn-icon:hover { color: var(--danger); border-color: var(--danger); }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="mb-1">{{ $order->diningTable->name }} <span class="text-muted fw-normal">· Order #{{ $order->id }}</span></h3>
        <span class="badge bg-secondary text-uppercase" style="letter-spacing:.04em;">{{ str_replace('_',' ', $order->status) }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('staff.tables.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Tables</a>
        @if(in_array($order->status, ['served','finished']))
            <a href="{{ route('staff.orders.pay', $order) }}" class="btn btn-success btn-sm"><i class="bi bi-cash-coin"></i> Bill / Pay</a>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        @if(! in_array($order->status, ['paid', 'cancelled']))
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Menu</span>
                    <span class="text-muted small fw-normal">
                        @if($order->status === 'open')
                            Adding the same dish bumps its quantity
                        @else
                            <i class="bi bi-lightning-charge-fill text-warning"></i> Extra items fire straight to the kitchen
                        @endif
                    </span>
                </div>
                <div class="card-body menu-scroll">
                    @forelse($categories as $category)
                        @if($category->menuItems->isNotEmpty())
                            <div class="cat-label">{{ $category->name }} <span class="badge bg-light text-dark border">{{ $category->station }}</span></div>
                            <div class="row g-3 mb-2">
                                @foreach($category->menuItems as $menuItem)
                                    <div class="col-sm-6 col-xl-4">
                                        <form method="POST" action="{{ route('staff.orders.items.store', $order) }}" class="dish">
                                            @csrf
                                            <input type="hidden" name="menu_item_id" value="{{ $menuItem->id }}">
                                            <div class="dish-media">
                                                @if($menuItem->image)
                                                    <img src="{{ asset('storage/' . $menuItem->image) }}" alt="{{ $menuItem->name }}">
                                                @else
                                                    <span class="noimg"><i class="bi bi-egg-fried" style="font-size:2rem;"></i></span>
                                                @endif
                                                <span class="dish-price">${{ number_format($menuItem->price, 2) }}</span>
                                            </div>
                                            <div class="dish-body">
                                                <div>
                                                    <div class="dish-name">{{ $menuItem->name }}</div>
                                                    @if($menuItem->description)
                                                        <div class="dish-desc">{{ \Illuminate\Support\Str::limit($menuItem->description, 60) }}</div>
                                                    @endif
                                                </div>
                                                <div class="stepper">
                                                    <div class="qty">
                                                        <button type="button" onclick="this.nextElementSibling.stepDown()">&minus;</button>
                                                        <input type="number" name="quantity" value="1" min="1" max="99">
                                                        <button type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                                                    </div>
                                                    <button class="btn btn-dark btn-sm flex-fill"><i class="bi bi-plus-lg"></i> Add</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @empty
                        <p class="text-muted mb-0">No categories available.</p>
                    @endforelse
                </div>
            </div>
        @else
            <div class="alert alert-secondary">This order is {{ $order->status }} and can no longer be changed.</div>
        @endif
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm" style="position: sticky; top: 20px;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Current Order</span>
                <span class="text-muted small fw-normal">{{ $order->items->sum('quantity') }} item{{ $order->items->sum('quantity') === 1 ? '' : 's' }}</span>
            </div>
            <div class="card-body">
                @forelse($order->items as $item)
                    <div class="ticket-line">
                        <span class="ticket-qty">{{ $item->quantity }}&times;</span>
                        <div class="flex-grow-1">
                            <div class="ticket-name">{{ $item->menuItem->name }}</div>
                            @if($item->notes)<div class="ticket-note">{{ $item->notes }}</div>@endif
                            <div class="text-muted small">${{ number_format($item->price, 2) }} each</div>
                        </div>
                        <div class="text-end">
                            <div class="ticket-sub">${{ number_format($item->subtotal(), 2) }}</div>
                            @if(! in_array($order->status, ['paid', 'cancelled']))
                                <form method="POST" action="{{ route('staff.orders.items.destroy', [$order, $item]) }}" onsubmit="return confirm('Remove {{ $item->menuItem->name }}?')" class="mt-1">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon" title="Remove"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-basket" style="font-size:2rem; opacity:.35;"></i>
                        <p class="mb-0 mt-2 small">No items yet — pick from the menu.</p>
                    </div>
                @endforelse

                @if($order->items->isNotEmpty())
                    <div class="ticket-total">
                        <span class="text-uppercase small fw-bold text-muted" style="letter-spacing:.06em;">Total</span>
                        <span class="amt">${{ number_format($order->total, 2) }}</span>
                    </div>
                @endif
            </div>

            <div class="card-body border-top d-grid gap-2">
                @if($order->status === 'open')
                    <form method="POST" action="{{ route('staff.orders.send', $order) }}">
                        @csrf
                        <button class="btn btn-dark w-100 py-2" {{ $order->items->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-send"></i> Send Order to Kitchen
                        </button>
                    </form>
                @elseif($order->status === 'finished')
                    <form method="POST" action="{{ route('staff.orders.serve', $order) }}">
                        @csrf
                        <button class="btn btn-success w-100 py-2"><i class="bi bi-check2-circle"></i> Mark as Served</button>
                    </form>
                @elseif(in_array($order->status, ['sent_to_kitchen','accepted','preparing']))
                    <div class="alert alert-warning mb-0 text-center">Waiting on the kitchen: <strong>{{ str_replace('_',' ', $order->status) }}</strong></div>
                @elseif($order->status === 'served')
                    <div class="alert alert-success mb-0 text-center">Served — ready to bill.</div>
                @elseif($order->status === 'paid')
                    <div class="alert alert-secondary mb-0 text-center">Paid in full.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
