<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Waiter' }} · POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#FAF7F2] text-[#26241F] font-sans antialiased">
    <div class="flex h-screen overflow-hidden">

        {{-- Left icon rail --}}
        <nav class="w-20 shrink-0 bg-[#26241F] flex flex-col items-center py-6 gap-2">
            <a href="{{ route('tables.index') }}"
               class="w-12 h-12 flex items-center justify-center rounded-xl text-[#FAF7F2]/70 hover:text-[#FAF7F2] hover:bg-white/10 transition"
               title="Tables">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </a>
            <a href="{{ route('waiter.orders.create') }}"
               class="w-12 h-12 flex items-center justify-center rounded-xl bg-[#B8752F] text-white shadow-sm"
               title="New Order">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 4v16m8-8H4" />
                </svg>
            </a>
            <a href="{{ route('waiter.dashboard') }}"
               class="w-12 h-12 flex items-center justify-center rounded-xl text-[#FAF7F2]/70 hover:text-[#FAF7F2] hover:bg-white/10 transition"
               title="Active Orders">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6M9 8h6M5 4h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1z" />
                </svg>
            </a>

            <div class="mt-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-12 h-12 flex items-center justify-center rounded-xl text-[#FAF7F2]/50 hover:text-[#FAF7F2] hover:bg-white/10 transition"
                            title="Log out">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </nav>

        {{-- Main content --}}
        <main class="flex-1 overflow-hidden">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>