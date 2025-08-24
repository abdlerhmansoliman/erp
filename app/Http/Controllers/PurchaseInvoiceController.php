<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseInvoiceRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseInvoiceResource;
use App\Http\Resources\SalesInvoiceResource;
use App\Services\PurchaseInvoiceService;
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

public function store(PurchaseInvoiceRequest $request)
{
    
    $invoice = $this->purchaseInvoiceService->createInvoice($request->validated())
        ->load(['supplier', 'items']);
    return new PurchaseInvoiceResource($invoice);
}

public function update(UpdatePurchaseRequest $request, $id)
{
    $invoice = $this->purchaseInvoiceService->updateInvoice($id, $request->validated());

    if (!$invoice) {
        return response()->json(['message' => 'Invoice not found'], 404);
    }

    $invoice->load(['supplier', 'items']);
    return new PurchaseInvoiceResource($invoice);
}
    public function destroy($id)
    {
        $this->purchaseInvoiceService->deletePurchase($id);
        return response()->json(['message' => 'Purchase deleted successfully']);
    }
}