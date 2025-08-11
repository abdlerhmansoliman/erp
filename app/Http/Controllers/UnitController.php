<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitStoreRequest;
use App\Http\Requests\UnitUpdateRequest;
use App\Services\UnitService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
     protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->unitService->getAllUnits()
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->unitService->getUnitById($id)
        ]);
    }

    public function store(UnitStoreRequest $request)
    {
        $data = $request->validated();
        return response()->json([
            'status' => 'success',
            'data' => $this->unitService->createUnit($data)
        ]);
    }

    public function update(UnitUpdateRequest $request, $id)
    {
        $data = $request->validated();

        return response()->json([
            'status' => 'success',
            'data' => $this->unitService->updateUnit($id, $data)
        ]);
    }

    public function destroy($id)
    {
        $this->unitService->deleteUnit($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Unit deleted successfully'
        ]);
    }
}
