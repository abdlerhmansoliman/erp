<?php

use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('stocks', StockController::class);

Route::apiResource('units', UnitController::class);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/{id}/tax', [ProductController::class, 'getTax']);

Route::get('purchases/create', [PurchaseInvoiceController::class, 'create']);
Route::apiResource('purchases', PurchaseInvoiceController::class);
Route::post('purchases/delete-multiple', [PurchaseInvoiceController::class, 'deleteMultiple']);
Route::get('/purchases/{id}/pdf', [PurchaseInvoiceController::class, 'downloadPdf']);


Route::get('sales/create', [SalesInvoiceController::class, 'create']);
Route::apiResource('sales', SalesInvoiceController::class);
Route::get('/sales/{id}/pdf', [SalesInvoiceController::class, 'downloadPdf']);

Route::apiResource('products', ProductController::class);

Route::middleware('auth:sanctum')->get('/auth/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->user(),
    ]);
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/hello', function () {
    return response()->json(['message' => 'Hello API']);
});
    Route::prefix('auth')->group(function () {
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});

Route::get('/employees',[EmployerController::class,'index']);
Route::get('/employee/{id}',[EmployerController::class,'show']);
Route::post('/employee',[EmployerController::class,'store']);
Route::put('/employee/{id}',[EmployerController::class,'update']);
Route::delete('/employee/{id}',[EmployerController::class,'destroy']);
Route::get('/employee/{id}/department', [EmployerController::class, 'getEmployeesByDepartment']);
Route::get('/employee/{id}/position', [EmployerController::class, 'getEmployeesByPosition']);

Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/department/{id}', [DepartmentController::class, 'show']);
Route::post('/department', [DepartmentController::class, 'store']);
Route::put('department/{id}', [DepartmentController::class, 'update']);
Route::delete('/department/{id}', [DepartmentController::class, 'destroy']);

Route::get('/positions', [PositionController::class, 'index']);
Route::get('/position/{id}', [PositionController::class, 'show']);
Route::post('/position', [PositionController::class, 'store']);
Route::put('/position/{id}', [PositionController::class, 'update']);
Route::delete('/position/{id}', [PositionController::class, 'destroy']);



Route::apiResource('categories', CategoryController::class);
Route::apiResource('warehouses', WarehouseController::class);
Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('customers', CustomerController::class);
Route::apiResource('taxes', TaxController::class);


Route::post('products/delete-multiple', [ProductController::class, 'deleteMultiple']);
Route::post('customers/delete-multiple', [CustomerController::class, 'deleteMultiple']);
Route::post('suppliers/delete-multiple', [SupplierController::class, 'deleteMultiple']);

Route::middleware(['auth:sanctum'])->group(function () {







});


Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
    });
    
    // User Role Management Routes
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
    // Query Routes
    Route::get('/roles/{role}/users', [UserRoleController::class, 'getUsersByRole'])->name('roles.users');
});

