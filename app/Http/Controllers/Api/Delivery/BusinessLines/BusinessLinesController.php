<?php

namespace App\Http\Controllers\Api\Delivery\BusinessLines;

use App\Http\Controllers\Controller;
use App\Http\Services\Delivery\BusinessLinesService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BusinessLinesController extends Controller
{
    public function __construct(BusinessLinesService $businessLinesService)
    {
        $this->businessLinesService = $businessLinesService;
    }
    public function cities()
    {
        return $this->businessLinesService->cities();
    }

    // q - термин
    // limit
    // code
    public function getCitiesByTerm(Request $request)
    {
        $body = $request->all();
        return $this->businessLinesService->getCitiesByTerm($body);
    }

    public function calculate(Request $request)
    {
        $params = $request->all();
        return $this->businessLinesService->calculate($params);
    }


}
