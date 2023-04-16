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

    public function pvzList()
    {
        $latitude['to'] = 55.272220;
        $latitude['from'] = 55.073802;

        $longitude['from'] = 61.236601;
        $longitude['to'] = 61.510408;

        $body = [
            'latitude' => [
                'from' => $latitude['from'],
                'to' => $latitude['to'],
            ],
            'longitude' => [
                'from' => $longitude['from'],
                'to' => $longitude['to'],
            ],
            'available_for_dropoff' => true,
            'type' => 'terminal'
        ];

        return $this->service->pvzList($body);
    }
}
