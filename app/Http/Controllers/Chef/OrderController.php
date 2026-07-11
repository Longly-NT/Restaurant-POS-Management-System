<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $pending = Order::with(['diningTable', 'items.menuItem.category'])
            ->where('status', 'sent_to_kitchen')
            ->oldest('sent_to_kitchen_at')
            ->get();

        $active = Order::with(['diningTable', 'items.menuItem.category'])
            ->whereIn('status', ['accepted', 'preparing'])
            ->oldest('accepted_at')
            ->get();

        $finished = Order::with(['diningTable', 'items.menuItem.category'])
            ->where('status', 'finished')
            ->latest('finished_at')
            ->take(10)
            ->get();

        return view('chef.orders.index', compact('pending', 'active', 'finished'));
    }

    public function accept(Order $order)
    {
        abort_unless($order->status === 'sent_to_kitchen', 422, 'Order is not awaiting acceptance.');

        $order->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return back()->with('status', 'Order accepted.');
    }

    public function preparing(Order $order)
    {
        abort_unless($order->status === 'accepted', 422, 'Order must be accepted first.');

        $order->update(['status' => 'preparing']);

        return back()->with('status', 'Order marked as preparing.');
    }

    public function finished(Order $order)
    {
        abort_unless($order->status === 'preparing', 422, 'Order must be preparing first.');

        $order->update([
            'status' => 'finished',
            'finished_at' => now(),
        ]);

        return back()->with('status', 'Order marked as finished. Staff can now serve it.');
    }
}
