<?php

namespace App\Http\Controllers\Api\Delivery\BusinessLines;

use App\Http\Controllers\Controller;
use App\Http\Services\Delivery\BusinessLinesService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

    public function street(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cityID' => 'required|integer',
            'street' => 'required|string',
        ]);
        if ($validator->fails()) throw ValidationException::withMessages([
           $validator->errors()
        ]);
        $data = $validator->validated();
        return $this->businessLinesService->getStreet($data);
    }

    public function getTerminals(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'direction' => ['required', Rule::in('arrival', 'derival')]
        ]);
        if ($validator->fails()) throw ValidationException::withMessages([
            $validator->errors()
        ]);
        $data = $validator->validated();
        return $this->businessLinesService->getTerminals($data);
    }
}
