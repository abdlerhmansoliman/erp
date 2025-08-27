<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseInvoiceRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseInvoiceResource;
use App\Http\Resources\PurchaseItemResource;
use App\Http\Resources\SalesInvoiceResource;
use App\Services\PurchaseInvoiceService;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function __construct(protected PurchaseInvoiceService $purchaseInvoiceService){}
   public function index()
{
    $filters = request()->only(['search', 'sortBy', 'sortDir', 'perPage', 'page']);

    $invoices = $this->purchaseInvoiceService->getAllInvoices($filters);
        
        
    return PurchaseInvoiceResource::collection($invoices);
    }
    public function show($id)
    {
        try {
            $invoice = $this->purchaseInvoiceService->getInvoiceByIdWithItems($id);
            if(!$invoice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Purchase invoice not found',
                ], 404);
            }
            return response()->json([
                'status' => 'success',
                'data' => new PurchaseItemResource($invoice)
            ]);

        } catch (\Throwable $e) {
            return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch purchase invoice',
            'errors' => [$e->getMessage()],
        ], 500);
        }
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
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create purchase invoice',
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