<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserRoleController,
    RoleController,
    EmployerController,
    DepartmentController,
    PositionController,
    StockController,
    UnitController,
    ProductController,
    CategoryController,
    WarehouseController,
    SupplierController,
    CustomerController,
    TaxController,
    PurchaseInvoiceController,
    PurchaseReturnController,
    SalesInvoiceController,
};

// =============================
// Public Routes
// =============================
Route::get('/hello', fn () => response()->json(['message' => 'Hello API']));

// =============================
// Auth Routes
// =============================
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});

// =============================
// Protected Routes (require login)
// =============================
Route::middleware(['auth:sanctum'])->group(function () {

    // Roles & Permissions
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
    });

    Route::prefix('users')->group(function () {
        Route::get('/{user}/permissions', [UserRoleController::class, 'getUserPermissions']);
        Route::post('/{user}/roles', [UserRoleController::class, 'assignRoles']);
        Route::put('/{user}/roles', [UserRoleController::class, 'syncRoles']);
        Route::delete('/{user}/roles', [UserRoleController::class, 'removeRoles']);
        Route::post('/{user}/permissions', [UserRoleController::class, 'assignPermissions']);
        Route::put('/{user}/permissions', [UserRoleController::class, 'syncPermissions']);
    });

    Route::post('/bulk/assign-role', [UserRoleController::class, 'bulkAssignRole']);

    // Employers
    Route::apiResource('employees', EmployerController::class);
    Route::get('/employee/{id}/department', [EmployerController::class, 'getEmployeesByDepartment']);
    Route::get('/employee/{id}/position', [EmployerController::class, 'getEmployeesByPosition']);

    // Departments & Positions
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('positions', PositionController::class);

    // Stocks & Units
    Route::apiResource('stocks', StockController::class);
    Route::apiResource('units', UnitController::class);

    // Products & Categories
    Route::apiResource('products', ProductController::class);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/{id}/tax', [ProductController::class, 'getTax']);
    Route::post('products/delete-multiple', [ProductController::class, 'deleteMultiple']);

    Route::apiResource('categories', CategoryController::class);

    // Warehouses
    Route::apiResource('warehouses', WarehouseController::class);

    // Suppliers & Customers
    Route::apiResource('suppliers', SupplierController::class);
    Route::post('suppliers/delete-multiple', [SupplierController::class, 'deleteMultiple']);

    Route::apiResource('customers', CustomerController::class);
    Route::post('customers/delete-multiple', [CustomerController::class, 'deleteMultiple']);

    // Taxes
    Route::apiResource('taxes', TaxController::class);

    // Purchases
    Route::get('purchases/create', [PurchaseInvoiceController::class, 'create']);
    Route::apiResource('purchases', PurchaseInvoiceController::class);
    Route::post('purchases/delete-multiple', [PurchaseInvoiceController::class, 'deleteMultiple']);
    Route::get('/purchases/{id}/pdf', [PurchaseInvoiceController::class, 'downloadPdf']);

    // Purchase Returns
    Route::apiResource('returns/purchase', PurchaseReturnController::class);

    // Sales
    Route::get('sales/create', [SalesInvoiceController::class, 'create']);
    Route::apiResource('sales', SalesInvoiceController::class);
    Route::get('/sales/{id}/pdf', [SalesInvoiceController::class, 'downloadPdf']);
});
    Route::get('/returns/purchase/create/{id}', [PurchaseReturnController::class, 'createPurchaseReturn']);
