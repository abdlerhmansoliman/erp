<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseInvoiceRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseInvoiceResource;
use App\Http\Resources\SalesInvoiceResource;
use App\Services\PurchaseInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PurchaseInvoiceController extends Controller
{
    public function __construct(protected PurchaseInvoiceService $purchaseInvoiceService){}
   public function index()
{
    $filters = request()->only(['search', 'sortBy', 'sortDir', 'perPage', 'page']);
            Log::info('Purchase API - Filters:', $filters);

    $invoices = $this->purchaseInvoiceService->getAllInvoices($filters);
            Log::info('Purchase API - Raw data count:', ['count' => $invoices->count()]);
        
        
    return PurchaseInvoiceResource::collection($invoices);
}
    public function create()
    {
        $data = $this->purchaseInvoiceService->getCreateData();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
public function store(PurchaseInvoiceRequest $request)
{
    try {
        $invoice = $this->purchaseInvoiceService->createInvoice($request->validated())
            ->load(['supplier', 'items', 'warehouse']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Purchase invoice created successfully',
            'data' => new PurchaseInvoiceResource($invoice)
        ], 201);
    } catch (\Exception $e) {
        Log::error('Purchase Invoice Creation Error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create purchase invoice',
            'errors' => [$e->getMessage()]
        ], 500);
    }
}

public function update(UpdatePurchaseRequest $request, $id)
{
    try {
        $invoice = $this->purchaseInvoiceService->updateInvoice($id, $request->validated());

        if (!$invoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice not found'
            ], 404);
        }

        $invoice->load(['supplier', 'items', 'warehouse']);
        return response()->json([
            'status' => 'success',
            'message' => 'Purchase invoice updated successfully',
            'data' => new PurchaseInvoiceResource($invoice)
        ]);
    } catch (\Exception $e) {
        Log::error('Purchase Invoice Update Error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update purchase invoice',
            'errors' => [$e->getMessage()]
        ], 500);
    }
}
    public function destroy($id)
    {
        $this->purchaseInvoiceService->deletePurchase($id);
        return response()->json(['message' => 'Purchase deleted successfully']);
    }
    public function deleteMultiple(Request $request)
    {   
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No valid IDs provided'
            ], 400);
        }
        $this->purchaseInvoiceService->deleteMultiplePurchases($ids);
        return response()->json(['message' => 'Selected purchases deleted successfully']);
    }
}