<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'staff_count' => User::whereIn('role', ['staff', 'chef'])->count(),
            'menu_items' => MenuItem::count(),
            'tables' => DiningTable::count(),
            'active_orders' => Order::whereNotIn('status', ['paid', 'cancelled'])->count(),
        ];

        $recentOrders = Order::with(['diningTable', 'user'])->latest()->take(10)->get();

        return view('manager.dashboard', compact('stats', 'recentOrders'));
    }
}
