<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'dining_table_id',
        'user_id',
        'status',
        'total',
        'sent_to_kitchen_at',
        'accepted_at',
        'finished_at',
        'served_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'sent_to_kitchen_at' => 'datetime',
            'accepted_at' => 'datetime',
            'finished_at' => 'datetime',
            'served_at' => 'datetime',
        ];
    }

    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function recalculateTotal(): void
    {
        $this->total = $this->items()->get()->sum(fn ($item) => $item->price * $item->quantity);
        $this->save();
    }

    public function amountPaid(): float
    {
        return (float) $this->payments()->sum('total_amount');
    }

    public function balanceDue(): float
    {
        return round((float) $this->total - $this->amountPaid(), 2);
    }

    public function itemsByStation(string $station)
    {
        return $this->items()->whereHas('menuItem.category', fn ($q) => $q->where('station', $station))->with('menuItem')->get();
    }
}
