<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesReturnRequest;
use App\Http\Resources\SalesReturnResource;
use App\Models\SalesReturn;
use App\Services\SalesRetrunService;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    public function __construct(protected SalesRetrunService $salesRetrunService){}
    public function index(){
        $request=request()->only(['search', 'sortBy', 'sortDir', 'perPage', 'page']);
        $returns=$this->salesRetrunService->getAllReturns($request);
        return SalesReturnResource::collection($returns);
    }

    public function show($id){
        $return=$this->salesRetrunService->show($id);
        return response()->json([
            'success' => true,
            'data' => new SalesReturnResource($return)
        ]);   
    }

    public function store(SalesReturnRequest $request){
        $data=$request->validated();
        $return=$this->salesRetrunService->create($data);

        return new SalesReturnResource($return);
    }
    public function create($id){

        $return=$this->salesRetrunService->prepareReturnData($id);
                if (!$return) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $return
        ]);
    
    }
}
