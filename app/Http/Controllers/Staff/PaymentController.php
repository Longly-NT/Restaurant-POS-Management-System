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

        // Use the order's original total as the canonical "subtotal" for
        // discount and tax calculations — discounts should always apply to the
        // order total (per business rule), not to the tendered amount.
        $orderSubtotal = (float) $order->total;
        $discountPercent = $data['discount_percent'] ?? 0.00;
        $discount = round($orderSubtotal * ($discountPercent / 100), 2);
        $tip = $data['tip_amount'] ?? 0.00;

        $taxableAmount = max($orderSubtotal - $discount, 0);
        $taxRate = config('pos.tax_rate', 0);
        $tax = round($taxableAmount * $taxRate, 2);

        $totalCollected = round($orderSubtotal - $discount + $tax + $tip, 2);

        // Ensure the tendered amount covers the computed total to collect.
        if (round($data['amount'], 2) < $totalCollected) {
            return back()->withErrors(['amount' => 'Enter an amount equal to or greater than the remaining total to collect.'])->withInput();
        }

        // Persist the detailed breakdown using the order subtotal as the base.
        $order->payments()->create([
            'subtotal_amount' => $orderSubtotal,
            'tendered_amount' => round($data['amount'], 2),
            'discount_amount' => $discount,
            'discount_percent' => $discountPercent,
            'discount_reason' => $discount > 0 ? ($data['discount_reason'] ?? 'Staff discount') : null,
            'tax_amount'      => $tax,
            'tip_amount'      => $tip,
            'total_amount'    => $totalCollected,
            'payment_method'  => $data['method'],
            'processed_by'    => auth()->id(),
            'refund_amount'   => 0.00,
        ]);

        if ($order->balanceDue() <= 0) {
            $order->update(['status' => 'paid']);
            $order->diningTable->update(['status' => 'available']);
        }

        return back()->with('status', 'Payment recorded.');
    }

   
}