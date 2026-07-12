@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-0">{{ $order->diningTable->name }} &middot; Order #{{ $order->id }}</h3>
        <span class="badge bg-secondary text-uppercase">{{ str_replace('_',' ', $order->status) }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('staff.tables.index') }}" class="btn btn-outline-secondary btn-sm">Back to Tables</a>
        @if(in_array($order->status, ['served','finished']) )
            <a href="{{ route('staff.orders.pay', $order) }}" class="btn btn-success btn-sm"><i class="bi bi-cash-coin"></i> Bill / Pay</a>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-md-7">
        @if($order->status === 'open')
            <div class="card shadow-sm mb-3">
                <div class="card-header">Add Items to Order</div>
                <div class="card-body" style="max-height: 460px; overflow-y:auto;">
                    @forelse($categories as $category)
                        @if($category->menuItems->isNotEmpty())
                            <h6 class="text-muted text-uppercase small mt-2">{{ $category->name }} <span class="badge bg-light text-dark">{{ $category->station }}</span></h6>
                            <div class="row g-2 mb-3">
                                @foreach($category->menuItems as $menuItem)
                                    <div class="col-6">
                                        <form method="POST" action="{{ route('staff.orders.items.store', $order) }}" class="border rounded p-2 h-100 d-flex flex-column justify-content-between">
                                            @csrf
                                            <input type="hidden" name="menu_item_id" value="{{ $menuItem->id }}">
                                            @if($menuItem->image)
                                                <img src="{{ asset('storage/' . $menuItem->image) }}" alt="{{ $menuItem->name }}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px; margin-bottom: 8px;">
                                            @endif
                                            <div>
                                                <div class="fw-semibold small">{{ $menuItem->name }}</div>
                                                <div class="text-muted small">${{ number_format($menuItem->price, 2) }}</div>
                                            </div>
                                            <div class="d-flex gap-1 mt-2">
                                                <input type="number" name="quantity" value="1" min="1" class="form-control form-control-sm" style="width:65px">
                                                <button class="btn btn-sm btn-dark flex-fill">Add</button>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @empty
                        <p class="text-muted">No categories available.</p>
                    @endforelse
                </div>
            </div>
        @else
            <div class="alert alert-info">This order has been sent and can no longer be edited here.</div>
        @endif
    </div>

    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header">Current Order</div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Item</th><th>Qty</th><th>Subtotal</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($order->items as $item)
                            <tr>
                                <td>{{ $item->menuItem->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->subtotal(), 2) }}</td>
                                <td class="text-end">
                                    @if($order->status === 'open')
                                        <form method="POST" action="{{ route('staff.orders.items.destroy', [$order, $item]) }}" onsubmit="return confirm('Remove item?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No items added yet.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total</th>
                            <th colspan="2">${{ number_format($order->total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-body d-grid gap-2">
                @if($order->status === 'open')
                    <form method="POST" action="{{ route('staff.orders.send', $order) }}">
                        @csrf
                        <button class="btn btn-dark w-100" {{ $order->items->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-send"></i> Send Order to Kitchen
                        </button>
                    </form>
                @elseif($order->status === 'finished')
                    <form method="POST" action="{{ route('staff.orders.serve', $order) }}">
                        @csrf
                        <button class="btn btn-success w-100"><i class="bi bi-check2-circle"></i> Mark as Served</button>
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
