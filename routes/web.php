<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Auth (Guest only) ────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// ── Root redirect ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ── Protected routes ──────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

    // ── Admin-only routes (client users are redirected to client.dashboard) ──
    Route::middleware('admin.only')->group(function () {

        // ── Role Master ──────────────────────────────────────────────────────────
        Route::prefix('roles')->group(function () {
            Route::get('/',               [App\Http\Controllers\RoleMasterController::class, 'index'])->name('roles.index');
            Route::get('/allData',        [App\Http\Controllers\RoleMasterController::class, 'allData'])->name('roles.allData');
            Route::get('/create',         [App\Http\Controllers\RoleMasterController::class, 'create'])->name('roles.create');
            Route::post('/',              [App\Http\Controllers\RoleMasterController::class, 'store'])->name('roles.store');
            Route::post('/update-status', [App\Http\Controllers\RoleMasterController::class, 'updateStatus'])->name('roles.updateStatus');
            Route::get('/{id}',           [App\Http\Controllers\RoleMasterController::class, 'edit'])->name('roles.edit');
            Route::post('/{id}',          [App\Http\Controllers\RoleMasterController::class, 'update'])->name('roles.update');
            Route::post('/{id}/delete',   [App\Http\Controllers\RoleMasterController::class, 'destroy'])->name('roles.destroy');
        });

        // ── Permission Master ────────────────────────────────────────────────────
        Route::prefix('permissions')->group(function () {
            Route::get('/',          [App\Http\Controllers\PermissionMasterController::class, 'index'])->name('permissions.index');
            Route::get('/{roleId}',  [App\Http\Controllers\PermissionMasterController::class, 'edit'])->name('permissions.edit');
            Route::post('/{roleId}', [App\Http\Controllers\PermissionMasterController::class, 'update'])->name('permissions.update');
        });

        // ── Client Master ────────────────────────────────────────────────────────
        Route::prefix('clients')->group(function () {
            Route::get('/',               [App\Http\Controllers\ClientMasterController::class, 'index'])->name('clients.index');
            Route::get('/allData',        [App\Http\Controllers\ClientMasterController::class, 'allData'])->name('clients.allData');
            Route::get('/create',         [App\Http\Controllers\ClientMasterController::class, 'create'])->name('clients.create');
            Route::get('/map',            [App\Http\Controllers\ClientMapController::class,    'index'])->name('clients.map');
            Route::get('/map/data',       [App\Http\Controllers\ClientMapController::class,    'data'])->name('clients.map.data');
            Route::post('/',              [App\Http\Controllers\ClientMasterController::class, 'store'])->name('clients.store');
            Route::post('/update-status', [App\Http\Controllers\ClientMasterController::class, 'updateStatus'])->name('clients.updateStatus');
            Route::get('/{id}',           [App\Http\Controllers\ClientMasterController::class, 'edit'])->name('clients.edit');
            Route::post('/{id}',          [App\Http\Controllers\ClientMasterController::class, 'update'])->name('clients.update');
            Route::post('/{id}/delete',   [App\Http\Controllers\ClientMasterController::class, 'destroy'])->name('clients.destroy');
        });

        // ── Employee Master ──────────────────────────────────────────────────────
        Route::prefix('employees')->group(function () {
            Route::get('/',               [App\Http\Controllers\EmployeeMasterController::class, 'index'])->name('employees.index');
            Route::get('/allData',        [App\Http\Controllers\EmployeeMasterController::class, 'allData'])->name('employees.allData');
            Route::get('/create',         [App\Http\Controllers\EmployeeMasterController::class, 'create'])->name('employees.create');
            Route::post('/',              [App\Http\Controllers\EmployeeMasterController::class, 'store'])->name('employees.store');
            Route::post('/update-status', [App\Http\Controllers\EmployeeMasterController::class, 'updateStatus'])->name('employees.updateStatus');
            Route::get('/{id}',           [App\Http\Controllers\EmployeeMasterController::class, 'edit'])->name('employees.edit');
            Route::post('/{id}',          [App\Http\Controllers\EmployeeMasterController::class, 'update'])->name('employees.update');
            Route::post('/{id}/delete',   [App\Http\Controllers\EmployeeMasterController::class, 'destroy'])->name('employees.destroy');
        });

        // ── Table Type Master ────────────────────────────────────────────────────
        Route::prefix('table-types')->group(function () {
            Route::get('/',               [App\Http\Controllers\TableTypeMasterController::class, 'index'])->name('table-types.index');
            Route::get('/allData',        [App\Http\Controllers\TableTypeMasterController::class, 'allData'])->name('table-types.allData');
            Route::get('/create',         [App\Http\Controllers\TableTypeMasterController::class, 'create'])->name('table-types.create');
            Route::post('/',              [App\Http\Controllers\TableTypeMasterController::class, 'store'])->name('table-types.store');
            Route::post('/update-status', [App\Http\Controllers\TableTypeMasterController::class, 'updateStatus'])->name('table-types.updateStatus');
            Route::get('/{id}',           [App\Http\Controllers\TableTypeMasterController::class, 'edit'])->name('table-types.edit');
            Route::post('/{id}',          [App\Http\Controllers\TableTypeMasterController::class, 'update'])->name('table-types.update');
            Route::post('/{id}/delete',   [App\Http\Controllers\TableTypeMasterController::class, 'destroy'])->name('table-types.destroy');
        });

        // ── Table Master ─────────────────────────────────────────────────────────
        Route::prefix('seating')->group(function () {
            Route::get('/',               [App\Http\Controllers\TableMasterController::class, 'index'])->name('tables.index');
            Route::get('/list',           [App\Http\Controllers\TableMasterController::class, 'allData'])->name('tables.allData');
            Route::get('/new',            [App\Http\Controllers\TableMasterController::class, 'create'])->name('tables.create');
            Route::post('/save',          [App\Http\Controllers\TableMasterController::class, 'store'])->name('tables.store');
            Route::post('/toggle-status', [App\Http\Controllers\TableMasterController::class, 'updateStatus'])->name('tables.updateStatus');
            Route::get('/{id}/edit',      [App\Http\Controllers\TableMasterController::class, 'edit'])->name('tables.edit');
            Route::post('/{id}/update',   [App\Http\Controllers\TableMasterController::class, 'update'])->name('tables.update');
            Route::post('/{id}/remove',   [App\Http\Controllers\TableMasterController::class, 'destroy'])->name('tables.destroy');
        });

        // ── Printers ─────────────────────────────────────────────────────────────
        Route::get('/printers', [App\Http\Controllers\PrinterStatusController::class, 'index'])->name('printers.index');

        // ── App Settings ─────────────────────────────────────────────────────────
        Route::prefix('settings')->group(function () {
            Route::get ('/upi',               [App\Http\Controllers\AppSettingsController::class, 'upi'])->name('settings.upi');
            Route::post('/upi',               [App\Http\Controllers\AppSettingsController::class, 'updateUpi'])->name('settings.upi.update');
            Route::post('/upi/client-amount', [App\Http\Controllers\AppSettingsController::class, 'updateClientAmount'])->name('settings.upi.client-amount');
        });

    }); // end admin.only

    // ── Shared routes (admin + client users both allowed) ────────────────────

    // ── Category Master ──────────────────────────────────────────────────────
    Route::prefix('categories')->group(function () {
        Route::get('/',               [App\Http\Controllers\CategoryMasterController::class, 'index'])->name('categories.index');
        Route::get('/allData',        [App\Http\Controllers\CategoryMasterController::class, 'allData'])->name('categories.allData');
        Route::get('/create',         [App\Http\Controllers\CategoryMasterController::class, 'create'])->name('categories.create');
        Route::post('/',              [App\Http\Controllers\CategoryMasterController::class, 'store'])->name('categories.store');
        Route::post('/update-status', [App\Http\Controllers\CategoryMasterController::class, 'updateStatus'])->name('categories.updateStatus');
        Route::get('/{id}',           [App\Http\Controllers\CategoryMasterController::class, 'edit'])->name('categories.edit');
        Route::post('/{id}',          [App\Http\Controllers\CategoryMasterController::class, 'update'])->name('categories.update');
        Route::post('/{id}/delete',   [App\Http\Controllers\CategoryMasterController::class, 'destroy'])->name('categories.destroy');
    });

    // ── Order Reports ────────────────────────────────────────────────────────
    Route::prefix('reports')->group(function () {
        Route::get('/orders',      [App\Http\Controllers\OrderReportController::class, 'index'])->name('reports.orders');
        Route::get('/orders/data', [App\Http\Controllers\OrderReportController::class, 'allData'])->name('reports.orders.data');
    });

    // ── Menu Master ──────────────────────────────────────────────────────────
    Route::prefix('menus')->group(function () {
        Route::get('/',               [App\Http\Controllers\MenuMasterController::class, 'index'])->name('menus.index');
        Route::get('/allData',        [App\Http\Controllers\MenuMasterController::class, 'allData'])->name('menus.allData');
        Route::get('/create',         [App\Http\Controllers\MenuMasterController::class, 'create'])->name('menus.create');
        Route::post('/',              [App\Http\Controllers\MenuMasterController::class, 'store'])->name('menus.store');
        Route::post('/update-status', [App\Http\Controllers\MenuMasterController::class, 'updateStatus'])->name('menus.updateStatus');
        Route::get('/{id}',           [App\Http\Controllers\MenuMasterController::class, 'edit'])->name('menus.edit');
        Route::post('/{id}',          [App\Http\Controllers\MenuMasterController::class, 'update'])->name('menus.update');
        Route::post('/{id}/delete',   [App\Http\Controllers\MenuMasterController::class, 'destroy'])->name('menus.destroy');
    });

    // ── Client Portal ────────────────────────────────────────────────────────────
    Route::prefix('client')->middleware('client.access')->group(function () {

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])
            ->name('client.dashboard');

        // Menu Report
        Route::prefix('menu-report')->group(function () {
            Route::get('/',            [App\Http\Controllers\Client\MenuReportController::class, 'index'])
                ->name('client.menu-report.index');
            Route::get('/data',        [App\Http\Controllers\Client\MenuReportController::class, 'data'])
                ->name('client.menu-report.data');
            Route::get('/top-selling', [App\Http\Controllers\Client\MenuReportController::class, 'topSelling'])
                ->name('client.menu-report.topSelling');
        });

        // Table Orders
        Route::prefix('table')->group(function () {
            Route::get('/{tableId}',                   [App\Http\Controllers\Client\TableOrderController::class, 'show'])->name('client.table.show');
            Route::post('/{tableId}/order',            [App\Http\Controllers\Client\TableOrderController::class, 'placeOrder'])->name('client.table.placeOrder');
            Route::post('/order/{orderId}/status',     [App\Http\Controllers\Client\TableOrderController::class, 'updateStatus'])->name('client.order.updateStatus');
            Route::post('/order/{orderId}/checkout',   [App\Http\Controllers\Client\TableOrderController::class, 'checkout'])->name('client.order.checkout');
            Route::post('/item/{itemId}/remove',       [App\Http\Controllers\Client\TableOrderController::class, 'removeItem'])->name('client.order.removeItem');
        });

        // Client Employees (Staff)
        Route::prefix('employees')->group(function () {
            Route::get('/',               [App\Http\Controllers\Client\ClientEmployeeController::class, 'index'])
                ->name('client.employees.index');
            Route::get('/allData',        [App\Http\Controllers\Client\ClientEmployeeController::class, 'allData'])
                ->name('client.employees.allData');
            Route::get('/create',         [App\Http\Controllers\Client\ClientEmployeeController::class, 'create'])
                ->name('client.employees.create');
            Route::post('/',              [App\Http\Controllers\Client\ClientEmployeeController::class, 'store'])
                ->name('client.employees.store');
            Route::post('/update-status', [App\Http\Controllers\Client\ClientEmployeeController::class, 'updateStatus'])
                ->name('client.employees.updateStatus');
            Route::get('/{id}/edit',      [App\Http\Controllers\Client\ClientEmployeeController::class, 'edit'])
                ->name('client.employees.edit');
            Route::post('/{id}',          [App\Http\Controllers\Client\ClientEmployeeController::class, 'update'])
                ->name('client.employees.update');
            Route::post('/{id}/delete',   [App\Http\Controllers\Client\ClientEmployeeController::class, 'destroy'])
                ->name('client.employees.destroy');
        });

    });

    // ── Admin JSON API (used by admin panel AJAX / React Native admin app) ────
    Route::prefix('api/v1/admin')->group(function () {
        Route::get ('settings/upi',    [App\Http\Controllers\Api\UpiSettingsController::class, 'adminShow']);
        Route::put ('settings/upi',    [App\Http\Controllers\Api\UpiSettingsController::class, 'adminUpdate']);
        Route::post('print/upi-qr',    [App\Http\Controllers\Api\PrintJobController::class,   'createUpiQrJob']);
    });

});
