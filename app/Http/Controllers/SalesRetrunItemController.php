<?php

namespace App\Http\Controllers;

use App\Services\SalesRetrunItemService;

class SalesRetrunItemController extends Controller
{
    public function __construct(protected SalesRetrunItemService $salesReturn) {}
}
