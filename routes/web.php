<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// SSO Route from Master Dashboard
Route::middleware('guest')->group(function () {
    Route::get('/sso/{token}', [\App\Http\Controllers\SsoController::class, 'login'])->name('sso.login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (all roles)
|--------------------------------------------------------------------------
| Routes accessible by any authenticated user (cashier, manager, admin, owner).
| The 'active' middleware ensures deactivated users are blocked.
*/
Route::middleware(['auth', 'verified', 'active'])->group(function () {

    // Dashboard is now admin-only; Cashiers use POS directly.

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Sales — cashiers can view and create; admin can do full CRUD (policy-controlled)
    Route::get('sales/{sale}/receipt', [\App\Http\Controllers\SaleController::class, 'receipt'])->name('sales.receipt');
    Route::resource('sales', \App\Http\Controllers\SaleController::class);
    
    // Returns
    Route::resource('sale-returns', \App\Http\Controllers\SaleReturnController::class);

    // Z-Report (Shift Summary)
    Route::get('/reports/z-report', [\App\Http\Controllers\ReportController::class, 'zReport'])->name('reports.z-report');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (admin, manager, owner only)
|--------------------------------------------------------------------------
| Routes restricted to users with admin-level access.
| The 'admin' middleware checks for admin/manager/owner roles.
*/
Route::middleware(['auth', 'verified', 'active', 'admin'])->group(function () {

    // Dashboard (Admin only)
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Admin Reports
    Route::get('/reports/profit-loss', [\App\Http\Controllers\ReportController::class, 'profitAndLoss'])->name('reports.profit-loss');
    Route::get('/reports/inventory', [\App\Http\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');

    // Inventory & catalog management
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('products', \App\Http\Controllers\ProductController::class);

    // Customers & suppliers
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

    // Purchases
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);

    // User management (admin creates/manages cashier accounts)
    Route::resource('users', UserController::class);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

require __DIR__.'/auth.php';



