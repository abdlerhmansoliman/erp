<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesInvoiceRequest;
use App\Http\Requests\UpdateSalesInvoiceRequest;
use App\Http\Resources\SalesInvoiceResource;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Services\SalesInvoiceService;
use Illuminate\Http\Request;


class SelesInvoiceController extends Controller
{
    public function __construct(protected SalesInvoiceService $salesInvoiceService){}
    public function index()
    {
        $invoices = $this->salesInvoiceService->getAllInvoices();
        return SalesInvoiceResource::collection($invoices);
    }
    
  public function store(StoreSalesInvoiceRequest $request)
    {
        $invoice = $this->salesInvoiceService->createInvoice($request->validated());
        return new SalesInvoiceResource($invoice);
    }
    public function show($id)
    {
        $invoice = $this->salesInvoiceService->getInvoiceById($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        return new SalesInvoiceResource($invoice);
    }
    public function update(UpdateSalesInvoiceRequest $request, $id)
    {
        $invoice = $this->salesInvoiceService->updateInvoice($id, $request->validated());
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);        
        }
        return new SalesInvoiceResource($invoice);
    }
    public function destroy($id)
    {
        $invoice = $this->salesInvoiceService->deleteInvoice($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        return response()->json(['message' => 'Invoice deleted successfully']);
    }

}
