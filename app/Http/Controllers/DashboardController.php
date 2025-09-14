<?php

namespace App\Http\Controllers;


use App\Services\DashbordService;

class DashboardController extends Controller
{
    public function __construct(protected DashbordService $dashboardService)
    {
    }
    public function index(){
        $data = $this->dashboardService->getOverviewData();

        return response()->json($data);
    }
}
