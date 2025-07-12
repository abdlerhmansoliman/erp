<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Services\PositionService;

class PositionController extends Controller
{
    protected $positionService;
    public function __construct(PositionService $positionService)
    {
        $this->positionService = $positionService;
    }
    public function index()
    {
        $position=$this->positionService->getAllPosition();
        return response()->json($position);
    } 
    public function show($id)
    {
        $position=$this->positionService->getPositionById($id);
        if (!$position) {
            return response()->json(['message' => 'Position not found'], 404);
        }
        return response()->json($position);
    }
    public function store(StorePositionRequest $request)
    {
        $date=$request->validated();
        $position=$this->positionService->createPosition($date);
        return response()->json([
            'message' => 'Position created successfully',
            'position' => $position
        ], 201);
        
    }
    public function update(UpdatePositionRequest $request, $id)
    {
        $date=$request->validated();
        $position=$this->positionService->updatePosition($id, $date);
        if (!$position) {
            return response()->json(['message' => 'Position not found'], 404);
        }
        return response()->json([
            'message' => 'Position updated successfully',
            'position' => $position
        ]);
    }
    public function destroy($id)
    {
        $position=$this->positionService->deletePosition($id);
        if (!$position) {
            return response()->json(['message' => 'Position not found'], 404);
        }
        return response()->json([
            'message' => 'Position deleted successfully',
            'position' => $position
        ]);
    }
}
