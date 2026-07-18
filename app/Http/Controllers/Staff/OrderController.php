<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DiningTable;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Open (or resume) an order for a table, then show the order builder.
     */
    public function openForTable(DiningTable $table)
    {
        $order = $table->orders()->whereNotIn('status', ['paid', 'cancelled'])->latest()->first();

        if (! $order) {
            $order = Order::create([
                'dining_table_id' => $table->id,
                'user_id' => Auth::id(),
                'status' => 'open',
            ]);

            $table->update(['status' => 'occupied']);
        }

        return redirect()->route('staff.orders.show', $order);
    }

    public function show(Order $order)
    {
        $order->load(['diningTable', 'items.menuItem.category', 'payments']);
        $categories = Category::with(['menuItems' => fn ($q) => $q->where('is_available', true)])->orderBy('name')->get();

        return view('staff.orders.show', compact('order', 'categories'));
    }

    public function myOrders()
    {
        $orders = Order::with('diningTable')
            ->where('user_id', Auth::id())
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->latest()
            ->get();

        return view('staff.orders.index', compact('orders'));
    }

    public function addItem(Request $request, Order $order)
    {
        $this->guardOpenForItems($order);

        $data = $request->validate([
            'menu_item_id' => ['required', 'exists:menu_items,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $menuItem = MenuItem::findOrFail($data['menu_item_id']);
        $quantity = $data['quantity'] ?? 1;
        $notes = $data['notes'] ?? null;

        // Merge with an existing line for the same menu item + notes instead of
        // creating a duplicate row: just bump the quantity.
        $existing = $order->items()
            ->where('menu_item_id', $menuItem->id)
            ->where('notes', $notes)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
        } else {
            $order->items()->create([
                'menu_item_id' => $menuItem->id,
                'quantity' => $quantity,
                'price' => $menuItem->price,
                'notes' => $notes,
            ]);
        }

        $order->recalculateTotal();

        // If the order was already sent (but the kitchen hasn't accepted it yet),
        // staff is adding extra items mid-service. Re-fire the ticket straight to
        // the kitchen — no chef approval needed. Once the kitchen has accepted the
        // ticket or started cooking it, guardOpenForItems() above already stopped
        // us from getting this far.
        if ($order->status !== 'open') {
            $order->update([
                'status' => 'sent_to_kitchen',
                'sent_to_kitchen_at' => now(),
                'accepted_at' => null,
                'finished_at' => null,
                'served_at' => null,
            ]);

            return back()->with('status', 'Item added and fired to the kitchen.');
        }

        return back()->with('status', 'Item added.');
    }

    public function removeItem(Order $order, OrderItem $item)
    {
        $this->guardOpenForItems($order);

        abort_unless($item->order_id === $order->id, 404);

        $item->delete();
        $order->recalculateTotal();

        return back()->with('status', 'Item removed.');
    }

    public function sendToKitchen(Order $order)
    {
        $this->guardEditable($order);

        abort_if($order->items()->count() === 0, 422, 'Add at least one item before sending to the kitchen.');

        $order->update([
            'status' => 'sent_to_kitchen',
            'sent_to_kitchen_at' => now(),
        ]);

        return redirect()->route('staff.orders.show', $order)->with('status', 'Order sent to the kitchen.');
    }

    public function markServed(Order $order)
    {
        abort_unless($order->status === 'finished', 422, 'Order is not ready to be served yet.');

        $order->update([
            'status' => 'served',
            'served_at' => now(),
        ]);

        return back()->with('status', 'Order marked as served.');
    }

    protected function guardEditable(Order $order): void
    {
        abort_unless($order->status === 'open', 422, 'This order can no longer be edited.');
    }

    /**
     * Items may be added/removed while the order is still open, or while it has
     * just been fired to the kitchen but not yet picked up. Once the kitchen has
     * accepted the ticket (status: accepted) or started cooking it (status:
     * preparing) — and for every status after that — the menu is locked so the
     * ticket in the kitchen always matches what staff see on screen.
     */
    protected function guardOpenForItems(Order $order): void
    {
        abort_unless(
            in_array($order->status, ['open', 'sent_to_kitchen']),
            422,
            'This order is already being prepared in the kitchen and can no longer be edited.'
        );
    }
}
