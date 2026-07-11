<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['diningTable', 'user', 'items.menuItem']);

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['diningTable', 'user', 'items.menuItem', 'payments']);

        return view('admin.orders.show', compact('order'));
    }
}
