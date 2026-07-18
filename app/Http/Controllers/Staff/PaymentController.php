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
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $subtotal = $data['amount'];
        $discountPercent = $data['discount_percent'] ?? 0.00;
        // Discounts are entered as a percentage (the common case) and resolved to
        // a dollar amount here, so the rest of the money math — and reporting —
        // keeps working the same way it always has.
        $discount = round($subtotal * ($discountPercent / 100), 2);
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
            'discount_percent' => $discountPercent,
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