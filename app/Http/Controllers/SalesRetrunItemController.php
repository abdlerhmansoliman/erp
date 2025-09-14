<?php

namespace App\Http\Controllers;

use App\Services\SalesRetrunItemService;
use Illuminate\Http\Request;

class SalesRetrunItemController extends Controller
{
    public function __construct(protected SalesRetrunItemService $salesReturn){}
    
}
