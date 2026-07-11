<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen font-sans overflow-hidden">

    <div class="flex h-full">

        <aside class="w-24 bg-white shadow-md flex flex-col items-center py-6 gap-8 z-10">
            <div class="bg-blue-600 text-white p-3 rounded-xl font-bold text-xl">POS</div>
            <nav class="flex flex-col gap-6 w-full">
                <a href="#" class="flex flex-col items-center text-blue-600 border-r-4 border-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-xs mt-1 font-semibold">Menu</span>
                </a>
                <a href="#" class="flex flex-col items-center text-gray-400 hover:text-blue-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span class="text-xs mt-1 font-semibold">Orders</span>
                </a>
                <a href="#" class="flex flex-col items-center text-gray-400 hover:text-blue-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-xs mt-1 font-semibold">Settings</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 flex flex-col p-6 overflow-hidden">
            <header class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Welcome, Cashier</h1>
                    <p class="text-gray-500 text-sm">Select items to create a new order</p>
                </div>
                <div class="relative w-72">
                    <input type="text" placeholder="Search menu..." class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </header>

            <div class="flex gap-4 mb-6 overflow-x-auto pb-2">
                <button class="bg-blue-600 text-white px-6 py-2 rounded-full font-semibold shadow whitespace-nowrap">All Items</button>
                <button class="bg-white text-gray-600 px-6 py-2 rounded-full font-semibold shadow hover:bg-blue-50 whitespace-nowrap">Burgers</button>
                <button class="bg-white text-gray-600 px-6 py-2 rounded-full font-semibold shadow hover:bg-blue-50 whitespace-nowrap">Drinks</button>
                <button class="bg-white text-gray-600 px-6 py-2 rounded-full font-semibold shadow hover:bg-blue-50 whitespace-nowrap">Desserts</button>
            </div>

            <div class="flex-1 overflow-y-auto pr-2">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md cursor-pointer transition p-4 border border-transparent hover:border-blue-500">
                        <div class="h-32 bg-gray-200 rounded-xl mb-4 flex items-center justify-center text-4xl">🍔</div>
                        <h3 class="font-bold text-gray-800">Classic Cheeseburger</h3>
                        <p class="text-blue-600 font-bold mt-2">$5.99</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md cursor-pointer transition p-4 border border-transparent hover:border-blue-500">
                        <div class="h-32 bg-gray-200 rounded-xl mb-4 flex items-center justify-center text-4xl">🍟</div>
                        <h3 class="font-bold text-gray-800">Large Fries</h3>
                        <p class="text-blue-600 font-bold mt-2">$2.99</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md cursor-pointer transition p-4 border border-transparent hover:border-blue-500">
                        <div class="h-32 bg-gray-200 rounded-xl mb-4 flex items-center justify-center text-4xl">🥤</div>
                        <h3 class="font-bold text-gray-800">Cola</h3>
                        <p class="text-blue-600 font-bold mt-2">$1.99</p>
                    </div>
                    </div>
            </div>
        </main>

        <aside class="w-96 bg-white shadow-l flex flex-col z-10">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Current Order</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Table 4</span>
            </div>
            
            <div class="flex-1 p-6 overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h4 class="font-bold text-gray-800">Classic Cheeseburger</h4>
                        <p class="text-sm text-gray-500">$5.99</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300">-</button>
                        <span class="font-semibold">2</span>
                        <button class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 hover:bg-blue-200">+</button>
                    </div>
                    <div class="font-bold text-gray-800">$11.98</div>
                </div>
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h4 class="font-bold text-gray-800">Cola</h4>
                        <p class="text-sm text-gray-500">$1.99</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300">-</button>
                        <span class="font-semibold">1</span>
                        <button class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 hover:bg-blue-200">+</button>
                    </div>
                    <div class="font-bold text-gray-800">$1.99</div>
                </div>
            </div>

            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <div class="flex justify-between mb-2 text-gray-600">
                    <span>Subtotal</span>
                    <span>$13.97</span>
                </div>
                <div class="flex justify-between mb-4 text-gray-600">
                    <span>Tax (10%)</span>
                    <span>$1.40</span>
                </div>
                <div class="flex justify-between mb-6 text-xl font-bold text-gray-800">
                    <span>Total</span>
                    <span>$15.37</span>
                </div>
                <button class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 transition">
                    Charge $15.37
                </button>
                <div class="flex gap-4 mt-4">
                    <button class="w-1/2 bg-red-100 text-red-600 font-semibold py-3 rounded-xl hover:bg-red-200 transition">Cancel</button>
                    <button class="w-1/2 bg-yellow-100 text-yellow-700 font-semibold py-3 rounded-xl hover:bg-yellow-200 transition">Hold Order</button>
                </div>
            </div>
        </aside>

    </div>

</body>
</html>