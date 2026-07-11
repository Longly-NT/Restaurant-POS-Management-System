<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Chef\OrderController as ChefOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Staff\PaymentController;
use App\Http\Controllers\Staff\TableController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Auth
Route::get('/login', [LoginController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// Admin & Manager (Accessible via the /admin/ prefix)
Route::middleware(['auth', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
    Route::post('menu-items', [MenuItemController::class, 'store'])->name('menu-items.store');
    Route::put('menu-items/{menuItem}', [MenuItemController::class, 'update'])->name('menu-items.update');
    Route::patch('menu-items/{menuItem}/toggle', [MenuItemController::class, 'toggleAvailability'])->name('menu-items.toggle');
    Route::delete('menu-items/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

    Route::get('reports', [ReportController::class, 'dailySummary'])->name('reports.index');
    Route::get('reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
});

// Staff
Route::middleware(['auth', 'role:staff,admin,manager'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('tables', [TableController::class, 'index'])->name('tables.index');
    Route::post('tables/{table}/open', [StaffOrderController::class, 'openForTable'])->name('tables.open');

    Route::get('my-orders', [StaffOrderController::class, 'myOrders'])->name('orders.mine');
    Route::get('orders/{order}', [StaffOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/items', [StaffOrderController::class, 'addItem'])->name('orders.items.store');
    Route::delete('orders/{order}/items/{item}', [StaffOrderController::class, 'removeItem'])->name('orders.items.destroy');
    Route::post('orders/{order}/send-to-kitchen', [StaffOrderController::class, 'sendToKitchen'])->name('orders.send');
    Route::post('orders/{order}/serve', [StaffOrderController::class, 'markServed'])->name('orders.serve');

    Route::get('orders/{order}/pay', [PaymentController::class, 'create'])->name('orders.pay');
    Route::post('orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
    Route::post('orders/{order}/split', [PaymentController::class, 'splitEvenly'])->name('orders.split');
});

// Chef
Route::middleware(['auth', 'role:chef,admin,manager'])->prefix('chef')->name('chef.')->group(function () {
    Route::get('orders', [ChefOrderController::class, 'index'])->name('orders.index');
    Route::post('orders/{order}/accept', [ChefOrderController::class, 'accept'])->name('orders.accept');
    Route::post('orders/{order}/preparing', [ChefOrderController::class, 'preparing'])->name('orders.preparing');
    Route::post('orders/{order}/finished', [ChefOrderController::class, 'finished'])->name('orders.finished');
});

// Catch-all Redirect for old Manager URL legacy links
Route::get('/manager/dashboard', function () {
    return redirect()->route('admin.dashboard');
});