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
        // 1. Validate ALL fields coming from your frontend HTML form
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,card,mobile'],
            'tip_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $subtotal = $data['amount'];
        $discount = $data['discount_amount'] ?? 0.00;
        $tip = $data['tip_amount'] ?? 0.00;

        // 2. Calculate tax based on the subtotal minus discount (matching your blade JS preview logic)
        $taxableAmount = max($subtotal - $discount, 0);
        $taxRate = config('pos.tax_rate', 0); // Pulls tax rate from config (e.g., 0.10 for 10%)
        $tax = round($taxableAmount * $taxRate, 2);

        // 3. Compute final total to collect
        $totalCollected = $subtotal - $discount + $tax + $tip;

        // Save mapping directly onto the detailed schema your model expects
        $order->payments()->create([
            'subtotal_amount' => $subtotal,
            'discount_amount' => $discount,
            'discount_reason' => $discount > 0 ? ($data['discount_reason'] ?? 'Staff discount') : null,
            'tax_amount'      => $tax,
            'tip_amount'      => $tip,
            'total_amount'    => $totalCollected,
            'payment_method'  => $data['method'],
            'processed_by'    => auth()->id(), // Keeps track of who checked out the user
            'refund_amount'   => 0.00,
        ]);

        if ($order->balanceDue() <= 0) {
            $order->update(['status' => 'paid']);
            $order->diningTable->update(['status' => 'available']);
        }

        return back()->with('status', 'Payment recorded.');
    }

   
}