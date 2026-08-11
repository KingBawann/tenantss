<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\SaleApiController;
use App\Http\Controllers\Api\PurchaseApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\SupplierApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — POS System
|--------------------------------------------------------------------------
*/

// ── Public (unauthenticated) ──────────────────────────────────────────
Route::post('/login', [AuthApiController::class, 'login']);

// ── Protected (Sanctum token required) ────────────────────────────────
Route::middleware(['auth:sanctum', 'active'])->name('api.')->group(function () {

    // Auth
    Route::post('/logout', [AuthApiController::class, 'logout'])->name('logout');
    Route::get('/user', [AuthApiController::class, 'user'])->name('user');

    // ── All Authenticated Users ───────────────────────────────────────
    // Products (read-only for cashiers — write ops protected by ProductPolicy)
    Route::apiResource('products', ProductApiController::class);

    // Sales (cashiers: create + view own; admins: full access — SalePolicy controlled)
    Route::apiResource('sales', SaleApiController::class)->except(['update']);

    // Customers (read for cashiers during POS, full CRUD policy-controlled)
    Route::apiResource('customers', CustomerApiController::class);


    // ── Admin-Only Routes ─────────────────────────────────────────────
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        // User management
        Route::apiResource('users', UserApiController::class);
        Route::post('users/{user}/reset-password', [UserApiController::class, 'resetPassword'])
            ->name('users.reset-password');
        Route::patch('users/{user}/toggle-status', [UserApiController::class, 'toggleStatus'])
            ->name('users.toggle-status');

        // Categories (full CRUD — admin only)
        Route::apiResource('categories', CategoryApiController::class);

        // Suppliers (admin only)
        Route::apiResource('suppliers', SupplierApiController::class);

        // Purchases (admin only)
        Route::apiResource('purchases', PurchaseApiController::class)->except(['update']);
    });
});
