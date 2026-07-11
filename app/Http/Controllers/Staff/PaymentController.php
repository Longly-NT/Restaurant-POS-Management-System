<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Order $order)
    {
        $order->load(['items.menuItem', 'payments', 'diningTable']);

        return view('staff.orders.pay', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        $data = $request->validate([
            'payer_label' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,card,mobile'],
        ]);

        $order->payments()->create($data);

        if ($order->balanceDue() <= 0) {
            $order->update(['status' => 'paid']);
            $order->diningTable->update(['status' => 'available']);
        }

        return back()->with('status', 'Payment recorded.');
    }

    public function splitEvenly(Request $request, Order $order)
    {
        $data = $request->validate([
            'splits' => ['required', 'integer', 'min:2', 'max:20'],
            'method' => ['required', 'in:cash,card,mobile'],
        ]);

        $remaining = $order->balanceDue();
        $each = round($remaining / $data['splits'], 2);

        for ($i = 1; $i <= $data['splits']; $i++) {
            $amount = $i === $data['splits']
                ? round($remaining - ($each * ($data['splits'] - 1)), 2)
                : $each;

            $order->payments()->create([
                'payer_label' => 'Guest '.$i,
                'amount' => $amount,
                'method' => $data['method'],
            ]);
        }

        $order->update(['status' => 'paid']);
        $order->diningTable->update(['status' => 'available']);

        return back()->with('status', 'Bill split and marked as paid.');
    }
}
