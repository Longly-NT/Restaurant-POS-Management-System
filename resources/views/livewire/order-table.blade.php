<div class="flex h-full" x-data="{ errorMsg: '' }"
     x-on:cart-error.window="errorMsg = $event.detail.message; setTimeout(() => errorMsg = '', 3000)">

    {{-- Error toast --}}
    <div x-show="errorMsg" x-cloak
         class="fixed top-4 left-1/2 -translate-x-1/2 bg-[#A23E3E] text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm">
        <span x-text="errorMsg"></span>
    </div>

    {{-- Center: category tabs + menu grid --}}
    <div class="flex-1 overflow-y-auto p-6">
        <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
            @foreach ($this->categories as $category)
                <button
                    wire:click="selectCategory({{ $category->id }})"
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition
                        {{ $activeCategoryId === $category->id
                            ? 'bg-[#26241F] text-white'
                            : 'bg-white text-[#26241F]/70 border border-[#26241F]/10 hover:border-[#26241F]/30' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @forelse ($this->menuItems as $item)
                <button
                    wire:click="addItem({{ $item->id }})"
                    class="text-left bg-white rounded-2xl p-4 border border-[#26241F]/10 hover:border-[#B8752F]/50 hover:shadow-md transition group">
                    <div class="flex items-start justify-between gap-2">
                        <span class="font-medium text-[#26241F] leading-snug">{{ $item->name }}</span>
                        <span class="w-7 h-7 shrink-0 rounded-full bg-[#B8752F]/10 text-[#B8752F] flex items-center justify-center group-hover:bg-[#B8752F] group-hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </span>
                    </div>
                    @if ($item->description)
                        <p class="text-sm text-[#26241F]/50 mt-1 line-clamp-2">{{ $item->description }}</p>
                    @endif
                    <p class="mt-3 font-semibold tabular-nums">${{ number_format($item->price, 2) }}</p>
                </button>
            @empty
                <p class="col-span-full text-center text-[#26241F]/40 py-12">No items in this category.</p>
            @endforelse
        </div>
    </div>

    {{-- Right: cart panel --}}
    <aside class="w-96 shrink-0 bg-white border-l border-[#26241F]/10 flex flex-col">
        <div class="p-5 border-b border-[#26241F]/10">
            <h2 class="font-semibold text-lg">Order Summary</h2>
            <p class="text-sm text-[#26241F]/50">Table #{{ $tableId }}</p>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            @forelse ($cart as $menuItemId => $line)
                <div class="border-b border-[#26241F]/5 pb-4">
                    <div class="flex items-start justify-between gap-2">
                        <span class="font-medium">{{ $line['name'] }}</span>
                        <button wire:click="removeItem({{ $menuItemId }})"
                                class="text-[#A23E3E]/60 hover:text-[#A23E3E] text-sm shrink-0">
                            Remove
                        </button>
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        {{-- Quantity stepper — sized for a thumb, not a mouse --}}
                        <div class="flex items-center gap-3">
                            <button wire:click="decrementQty({{ $menuItemId }})"
                                    class="w-9 h-9 rounded-full border border-[#26241F]/15 flex items-center justify-center text-lg leading-none hover:bg-[#26241F]/5">
                                −
                            </button>
                            <span class="w-6 text-center font-medium tabular-nums">{{ $line['qty'] }}</span>
                            <button wire:click="incrementQty({{ $menuItemId }})"
                                    class="w-9 h-9 rounded-full bg-[#B8752F] text-white flex items-center justify-center text-lg leading-none hover:bg-[#a1652a]">
                                +
                            </button>
                        </div>
                        <span class="font-semibold tabular-nums">
                            ${{ number_format($line['price'] * $line['qty'], 2) }}
                        </span>
                    </div>

                    <input
                        type="text"
                        wire:model.blur="cart.{{ $menuItemId }}.instructions"
                        placeholder="Special instructions (e.g. no chili)"
                        class="mt-2 w-full text-sm rounded-lg border-[#26241F]/10 focus:border-[#B8752F] focus:ring-[#B8752F]/20" />
                </div>
            @empty
                <p class="text-center text-[#26241F]/40 py-12 text-sm">
                    Tap a menu item to add it to this order.
                </p>
            @endforelse
        </div>

        <div class="p-5 border-t border-[#26241F]/10 space-y-2 bg-[#FAF7F2]">
            <div class="flex justify-between text-sm text-[#26241F]/60">
                <span>Subtotal</span>
                <span class="tabular-nums">${{ number_format($this->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-[#26241F]/60">
                <span>Tax</span>
                <span class="tabular-nums">${{ number_format($this->tax, 2) }}</span>
            </div>
            <div class="flex justify-between font-semibold text-lg pt-2 border-t border-[#26241F]/10">
                <span>Total</span>
                <span class="tabular-nums">${{ number_format($this->total, 2) }}</span>
            </div>

            <button
                wire:click="confirmOrder"
                @disabled(empty($cart))
                class="w-full mt-3 py-3 rounded-xl font-medium text-white transition
                    {{ empty($cart) ? 'bg-[#26241F]/20 cursor-not-allowed' : 'bg-[#3F5F4F] hover:bg-[#354f42]' }}">
                Confirm Order
            </button>
        </div>
    </aside>
</div>