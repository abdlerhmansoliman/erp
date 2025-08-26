<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxStoreRequest;
use App\Http\Requests\TaxUpdateRequest;
use App\Http\Resources\TaxResource;
use App\Services\TaxService;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    protected $taxService;

    public function __construct(TaxService $taxService)
    {
        $this->taxService = $taxService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'sortBy', 'sortDirection', 'perPage', 'page']);
        $taxes = $this->taxService->getAllTaxes($filters);

        return TaxResource::collection($taxes);
    }

    public function store(TaxStoreRequest $request)
    {
        $tax = $this->taxService->createTax($request->validated());
        return new TaxResource($tax);
    }

    public function show($id)
    {
        $tax = $this->taxService->getTaxById($id);
        return new TaxResource($tax);
    }

    public function update(TaxUpdateRequest $request, $id)
    {
        $tax = $this->taxService->updateTax($id, $request->validated());
        return new TaxResource($tax);
    }

    public function destroy($id)
    {
        $this->taxService->deleteTax($id);
        return response()->json(['message' => 'Tax deleted successfully']);
    }
}
