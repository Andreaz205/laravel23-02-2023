<?php

namespace App\Http\Controllers\Api\Delivery\Yandex;

use App\Http\Controllers\Controller;
use App\Http\Services\Delivery\YandexDeliveryService;
use Illuminate\Http\Request;

class YandexDeliveryController extends Controller
{
    public function __construct(YandexDeliveryService $service)
    {
        $this->service = $service;
    }

    public function getCoordinatesByTerm(Request $request)
    {
        $term = $request->input('term');
        return $this->service->getCoordinatesByTerm($term);
    }

    public function calculate(Request $request)
    {
        $params = $request->all();
        return $this->service->calculate($params);
    }


}
