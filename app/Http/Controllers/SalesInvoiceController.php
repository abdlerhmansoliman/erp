<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesInvoiceRequest;
use App\Http\Resources\SalesInvoiceResource;
use App\Services\SalesInvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesInvoiceController extends Controller
{
    public function __construct(protected SalesInvoiceService $salesInvoiceService) {}

    public function index()
    {
        $invoices = $this->salesInvoiceService->getAllInvoices();

        return SalesInvoiceResource::collection($invoices);
    }

    public function create()
    {
        $data = $this->salesInvoiceService->getCreateData();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function store(StoreSalesInvoiceRequest $request)
    {
        $invoice = $this->salesInvoiceService->createInvoice($request->validated());

        return new SalesInvoiceResource($invoice);
    }

    public function show($id)
    {
        $invoice = $this->salesInvoiceService->getInvoiceById($id);
        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        return new SalesInvoiceResource($invoice);
    }

    public function downloadPdf($id)
    {
        $invoice = $this->salesInvoiceService->getInvoiceByIdWithItems($id);
        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        try {
            $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $invoice]);

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Invoice-'.$invoice->invoice_number.'.pdf"');
        } catch (\Exception $e) {

            $e->getMessage();
        }
    }
}
