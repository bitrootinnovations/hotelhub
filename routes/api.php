<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TableTypeController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\TableStatusController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderTableController;
use App\Http\Controllers\Api\OrderReportController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ClientRoleController;
use App\Http\Controllers\Api\ClientEmployeeController;
use App\Http\Controllers\Api\MobilePermissionsController;
use App\Http\Controllers\Api\QuickOrderController;
use App\Http\Controllers\Api\PrinterController;
use App\Http\Controllers\Api\UpiSettingsController;
use App\Http\Controllers\Api\PrintJobController;

/*
|--------------------------------------------------------------------------
| HotelHub Mobile API — v1
|--------------------------------------------------------------------------
| Base URL  : /api/v1
| Auth      : JWT Bearer Token  (mobile.auth middleware)
| Who logs in:
|   - Client (restaurant owner)  → login_type = "client"
|   - Employee (waiter/manager)  → login_type = "employee"
|
| Token carries: user_type, client_id, name
| middleware resolves ClientMaster or ClientEmployee automatically
*/

Route::prefix('v1')->group(function () {

    // ── Public: Auth ────────────────────────────────────────────────────────
    Route::prefix('auth')->middleware('throttle:60,1')->group(function () {
        Route::post('login',   [AuthController::class, 'login']);
        Route::post('logout',  [AuthController::class, 'logout'])->middleware('mobile.auth');
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware('mobile.auth');
    });

    // ── Protected: Client & Employee ────────────────────────────────────────
    Route::middleware(['mobile.auth', 'throttle:120,1'])->group(function () {

        // Profile (works for both client and employee)
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);

        // Restaurant data — readable by both client and employees
        Route::get('categories',  [CategoryController::class,  'index']);
        Route::get('table-types', [TableTypeController::class, 'index']);
        Route::get('tables',      [TableController::class,     'index']);
        Route::get('menu',        [MenuController::class,      'index']);
        Route::get ('menu/{id}',  [MenuController::class, 'show']);
        Route::put ('menu/{id}',  [MenuController::class, 'update']);
        // POST with _method=PUT — required for React Native multipart/form-data file uploads
        Route::post('menu/{id}',  [MenuController::class, 'update']);

        // Table status (busy/free with color codes)
        Route::get('tables/status', [TableStatusController::class, 'index']);

        // Table orders — active items on a specific table
        Route::get('tables/{table_id}/orders', [OrderTableController::class, 'index']);

        // Orders — both client and employees can view and place orders
        Route::get ('orders',      [OrderController::class, 'index']);
        Route::post('orders',      [OrderController::class, 'store']);
        Route::get ('orders/{id}', [OrderController::class, 'show']);

        // Quick Orders — takeaway only, no table required
        Route::get ('quick-orders',      [QuickOrderController::class, 'index']);
        Route::post('quick-orders',      [QuickOrderController::class, 'store']);
        Route::get ('quick-orders/{id}', [QuickOrderController::class, 'show']);
        Route::put ('quick-orders/{id}', [QuickOrderController::class, 'update']);

        // Cart
        Route::get   ('cart',                [CartController::class, 'show']);
        Route::post  ('cart/items',          [CartController::class, 'addItem']);
        Route::delete('cart/items/{menu_id}',[CartController::class, 'removeItem']);
        Route::delete('cart',                [CartController::class, 'clear']);

        // Checkout
        Route::post('checkout',                  [CheckoutController::class, 'checkout']);
        Route::post('checkout/order/{order_id}', [CheckoutController::class, 'checkoutOrder']);

        // Printer status — called by mobile app on BLE connect/disconnect
        Route::post('printer/status', [PrinterController::class, 'updateStatus']);

        // UPI settings — for generating payment QR code on printer
        Route::get('settings/upi', [UpiSettingsController::class, 'show']);

        // Print jobs — mobile app polls and confirms completion
        Route::get('print/jobs/pending',      [PrintJobController::class, 'pending']);
        Route::put('print/jobs/{id}/done',    [PrintJobController::class, 'markDone']);

        // Reports
        Route::get('reports/orders', [OrderReportController::class, 'index']);

        // Permissions
        Route::get('permissions', [MobilePermissionsController::class, 'myPermissions']);

        // Client Role Master — client only (enforced inside controller)
        Route::prefix('client')->group(function () {
            Route::get   ('roles',       [ClientRoleController::class, 'index']);
            Route::post  ('roles',       [ClientRoleController::class, 'store']);
            Route::put   ('roles/{id}',  [ClientRoleController::class, 'update']);
            Route::delete('roles/{id}',  [ClientRoleController::class, 'destroy']);

            // Role permissions — client only
            Route::get('roles/{id}/permissions', [MobilePermissionsController::class, 'show']);
            Route::put('roles/{id}/permissions', [MobilePermissionsController::class, 'update']);

            // Client Employee Management — client only (enforced inside controller)
            Route::get   ('employees',      [ClientEmployeeController::class, 'index']);
            Route::post  ('employees',      [ClientEmployeeController::class, 'store']);
            Route::get   ('employees/{id}', [ClientEmployeeController::class, 'show']);
            Route::put   ('employees/{id}', [ClientEmployeeController::class, 'update']);
            Route::delete('employees/{id}', [ClientEmployeeController::class, 'destroy']);
        });
    });
});
