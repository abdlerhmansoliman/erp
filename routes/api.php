<?php

use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::prefix('payments')->group(function () {
    // إنشاء عملية دفع
    Route::post('/pay', [PaymentController::class, 'pay']);

    // تأكيد الدفع (Stripe confirm)
    Route::post('/{payment}/confirm', [PaymentController::class, 'confirm']);
});

// Stripe webhook endpoint
Route::post('/webhooks/stripe', [WebhookController::class, 'handleStripe']);
// Route::post('stripe/webhook', [StripeWebhookController::class, 'handle']);

Route::get('payment-methods', [PaymentController::class, 'methods']);
Route::post('payments', [PaymentController::class, 'store']);
// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google', [AuthController::class, 'loginWithGoogle']);
    
    // Authenticated auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authentication Required)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    
    // User information
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('dashboard/overview', [DashboardController::class, 'index']);
    
    /*
    |--------------------------------------------------------------------------
    | Inventory Management
    |--------------------------------------------------------------------------
    */
    
    // Products
    Route::prefix('products')->group(function () {
        Route::get('/search', [ProductController::class, 'search']);
        Route::get('/{id}/tax', [ProductController::class, 'getTax']);
        Route::post('/delete-multiple', [ProductController::class, 'deleteMultiple']);
    });
    Route::apiResource('products', ProductController::class);
    
    // Categories
    Route::apiResource('categories', CategoryController::class);
    
    // Units
    Route::apiResource('units', UnitController::class);
    
    // Stocks
    Route::apiResource('stocks', StockController::class);
    
    // Warehouses
    
    /*
    |--------------------------------------------------------------------------
    | Purchase Management
    |--------------------------------------------------------------------------
    */
    
    // Purchase Invoices
    Route::prefix('purchases')->group(function () {
        Route::get('/create', [PurchaseInvoiceController::class, 'create']);
        Route::post('/delete-multiple', [PurchaseInvoiceController::class, 'deleteMultiple']);
        Route::get('/{id}/pdf', [PurchaseInvoiceController::class, 'downloadPdf']);
    });
    
    Route::apiResource('purchases', PurchaseInvoiceController::class);
    
    // Purchase Returns
    Route::prefix('returns/purchase')->group(function () {
        Route::get('/create/{id}', [PurchaseReturnController::class, 'createPurchaseReturn']);
    });
    Route::apiResource('returns/purchase', PurchaseReturnController::class);
    
    /*
    |--------------------------------------------------------------------------
    | Sales Management
    |--------------------------------------------------------------------------
    */
    
    // Sales Invoices
    Route::prefix('sales')->group(function () {
        Route::get('/create', [SalesInvoiceController::class, 'create']);
        Route::get('/{id}/pdf', [SalesInvoiceController::class, 'downloadPdf']);
    });
    Route::apiResource('sales', SalesInvoiceController::class);
    
    // Sales Returns
    Route::prefix('returns/sales')->group(function () {
        Route::get('/create/{id}', [SalesReturnController::class, 'create']);
    });
    Route::apiResource('returns/sales', SalesReturnController::class);
    
    /*
    |--------------------------------------------------------------------------
    | Customer & Supplier Management
    |--------------------------------------------------------------------------
    */
    
    // Customers
    Route::prefix('customers')->group(function () {
        Route::post('/delete-multiple', [CustomerController::class, 'deleteMultiple']);
    });
    Route::apiResource('customers', CustomerController::class);
    
    // Suppliers
    Route::prefix('suppliers')->group(function () {
        Route::post('/delete-multiple', [SupplierController::class, 'deleteMultiple']);
    });
    Route::apiResource('suppliers', SupplierController::class);
       
    // Taxes
    Route::apiResource('taxes', TaxController::class);
    
    /*
    |--------------------------------------------------------------------------
    | Role & Permission Management
    |--------------------------------------------------------------------------
    */
    
    // Roles
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
        Route::get('/{role}/users', [UserRoleController::class, 'getUsersByRole'])->name('roles.users');
    });
    
    // User Role Management
    Route::prefix('users')->group(function () {
        Route::get('/{user}/permissions', [UserRoleController::class, 'getUserPermissions'])->name('users.permissions');
        Route::post('/{user}/roles', [UserRoleController::class, 'assignRoles'])->name('users.assign-roles');
        Route::put('/{user}/roles', [UserRoleController::class, 'syncRoles'])->name('users.sync-roles');
        Route::delete('/{user}/roles', [UserRoleController::class, 'removeRoles'])->name('users.remove-roles');
        Route::post('/{user}/permissions', [UserRoleController::class, 'assignPermissions'])->name('users.assign-permissions');
        Route::put('/{user}/permissions', [UserRoleController::class, 'syncPermissions'])->name('users.sync-permissions');
    });
    
    // Bulk Operations
    Route::post('/bulk/assign-role', [UserRoleController::class, 'bulkAssignRole'])->name('users.bulk-assign-role');

    // Warehouses
    Route::apiResource('/warehouses', WarehouseController::class);
    Route::get('/warehouses/{id}/edit', [WarehouseController::class, 'edit']);
});