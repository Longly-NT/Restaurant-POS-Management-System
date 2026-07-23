<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
    'order_id', 'subtotal_amount', 'tendered_amount', 'tax_amount', 'tip_amount',
    'discount_amount', 'discount_percent', 'discount_reason', 'discount_authorized_by',
    'refund_amount',
    'total_amount', 'payment_method', 'processed_by',
    ];

    protected $casts = [
        'subtotal_amount' => 'decimal:2',
        'tendered_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tip_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Relationship linking the transaction back to the 
     * user or employee who processed this payment.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /** What the guest actually handed over for this one payment. */
    public function totalCollected(): float
    {
        return round($this->subtotal_amount - $this->discount_amount + $this->tax_amount + $this->tip_amount, 2);
    }

    /**
     * Broken-out sales summary for a single date — gross/discounts/refunds/tax/tips/net,
     * never one lump total. Shared by ReportController (full report) and
     * DashboardController (today's snapshot) so the math only lives in one place.
     */
    public static function summaryForDate(string $date): array
    {
        $transactions = static::whereDate('created_at', $date)->get();

        return [
            'date' => $date,
            'transaction_count' => $transactions->count(),
            'gross_sales' => $transactions->sum('subtotal_amount'),
            'discounts' => $transactions->sum('discount_amount'),
            'refunds' => $transactions->sum('refund_amount'),
            'tax_collected' => $transactions->sum('tax_amount'),
            'tips' => $transactions->sum('tip_amount'),
            'net_sales' => $transactions->sum('total_amount') - $transactions->sum('refund_amount'),
        ];
    }
}
