<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\SelesInvoiceController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/hello', function () {
    return response()->json(['message' => 'Hello API']);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


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




Route::apiResource('products', ProductController::class);
Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('customers', CustomerController::class);

Route::post('/sales', [SelesInvoiceController::class, 'store']);
Route::get('/sales', [SelesInvoiceController::class, 'index']);
Route::put('/sales/{id}', [SelesInvoiceController::class, 'update']);
Route::get('/sales/{id}', [SelesInvoiceController::class, 'show']);


Route::post('/pruchases', [PurchaseInvoiceController::class, 'store']);
Route::get('/pruchases', [PurchaseInvoiceController::class, 'index']);
Route::put('/pruchases/{id}', [PurchaseInvoiceController::class, 'update']);
Route::delete('/pruchases/{id}', [PurchaseInvoiceController::class, 'destroy']);


