<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed; // [1] Import Computed Attribute

class OrderBuilder extends Component
{
    public ?int $tableId = null;
    public array $cart = [];
    public ?int $activeCategoryId = null;

    public function mount(RestaurantTable $table): void
    {
        $this->tableId = $table->id;
        $firstCategory = Category::orderBy('sort_order')->first();
        $this->activeCategoryId = $firstCategory?->id;
    }

    // Modern Livewire v3 Computed Properties
    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('sort_order')->get();
    }

    #[Computed]
    public function menuItems(): Collection
    {
        return MenuItem::available()
            ->where('category_id', $this->activeCategoryId)
            ->orderBy('name')
            ->get();
    }

    public function selectCategory(int $categoryId): void
    {
        $this->activeCategoryId = $categoryId;
    }

    public function addItem(int $menuItemId): void
    {
        $item = MenuItem::available()->find($menuItemId);

        if (! $item) {
            $this->dispatch('cart-error', message: 'That item is no longer available.');
            return;
        }

        if (isset($this->cart[$menuItemId])) {
            $this->cart[$menuItemId]['qty']++;
        } else {
            $this->cart[$menuItemId] = [
                'name' => $item->name,
                'price' => (float) $item->price, 
                'qty' => 1,
                'instructions' => '',
            ];
        }
    }

    public function incrementQty(int $menuItemId): void
    {
        $this->cart[$menuItemId]['qty']++;
    }

    public function decrementQty(int $menuItemId): void
    {
        if ($this->cart[$menuItemId]['qty'] <= 1) {
            $this->removeItem($menuItemId);
            return;
        }
        $this->cart[$menuItemId]['qty']--;
    }

    public function removeItem(int $menuItemId): void
    {
        unset($this->cart[$menuItemId]);
    }

    // Access these in Blade directly via $this->subtotal, $this->tax, etc.
    #[Computed]
    public function subtotal(): float
    {
        return collect($this->cart)->sum(fn ($line) => $line['price'] * $line['qty']);
    }

    #[Computed]
    public function tax(): float
    {
        return round($this->subtotal * config('pos.tax_rate'), 2);
    }

    #[Computed]
    public function total(): float
    {
        return round($this->subtotal + $this->tax, 2);
    }

    public function confirmOrder()
    {
        if (empty($this->cart)) {
            $this->dispatch('cart-error', message: 'Add at least one item before confirming.');
            return;
        }

        try {
            // [2 & 3] Wrap in a Transaction and use Pessimistic Row Locking
            return DB::transaction(function () {
                
                // lockForUpdate() prevents other requests from reading or changing this row until transaction commits
                $table = RestaurantTable::lockForUpdate()->findOrFail($this->tableId);

                if ($table->status->value === 'occupied') {
                    $this->dispatch('cart-error', message: 'This table already has an active order.');
                    return null; 
                }

                $order = Order::create([
                    'table_id' => $table->id,
                    'waiter_id' => auth()->id(),
                    'status' => 'pending',
                ]);

                foreach ($this->cart as $menuItemId => $line) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $menuItemId,
                        'quantity' => $line['qty'],
                        'unit_price' => $line['price'], 
                        'special_instructions' => $line['instructions'] ?: null,
                        'status' => 'pending',
                    ]);
                }

                $table->update(['status' => 'occupied']);

                return redirect()->route('waiter.dashboard');
            });

        } catch (\Exception $e) {
            // If anything fails inside the transaction block, database state completely reverts
            $this->dispatch('cart-error', message: 'An error occurred while creating the order. Please try again.');
            report($e);
        }
    }

    public function render()
    {
        return view('livewire.order-builder')
            ->layout('layouts.waiter', ['title' => 'New Order']);
    }
}