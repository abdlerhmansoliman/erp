<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseInvoiceRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseInvoiceResource;
use App\Http\Resources\SalesInvoiceResource;
use App\Services\PurchaseInvoiceService;


class PurchaseInvoiceController extends Controller
{
    public function __construct(protected PurchaseInvoiceService $purchaseInvoiceService){}
    public function index()
    {
        $invoices = $this->purchaseInvoiceService->getAllInvoices();
        return PurchaseInvoiceResource::collection($invoices);
    }

  public function store(PurchaseInvoiceRequest $request)
    {
        $invoice = $this->purchaseInvoiceService->createInvoice($request->validated());
        return new PurchaseInvoiceResource($invoice);
    }
    public function update(UpdatePurchaseRequest $request, $id)
    {
        $invoice = $this->purchaseInvoiceService->updateInvoice($id, $request->validated());
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);        
        }
        return new PurchaseInvoiceResource($invoice);
    }

}
