<?php

namespace App\Http\Services\Delivery;

use App\Library\YandexDelivery\Client;
use App\Models\YandexDeliveryData;

class YandexDeliveryService
{
    private Client $client;
    private float $xMin;
    private float $xMax;
    private float $yMin;
    private float $yMax;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->xMin = 61.236601;
        $this->xMax = 61.510408;
        $this->yMin = 55.073802;
        $this->yMax = 55.272220;
    }

    public function calculate($params)
    {
        $yandexDeliveryData = YandexDeliveryData::query()->first();
        $body = [
            'source' =>  [
                "platform_station" => [
                    "platform_id" => $yandexDeliveryData?->platform_id ?? "3ed47320-6e04-40eb-85ba-ed702cd0d7d2"
                ]
            ],
            "info" => [
                "operator_request_id" => "235353253532" //рандомное число так как заказа нет
            ],
            'billing_info' => [
                "payment_method" => $yandexDeliveryData?->payment_method ?? "already_paid"
            ],
            ...$params
        ];

//        dd($body);

        return $this->client->calculatePrice($body);
    }

    public function getCoordinatesByTerm($term)
    {
        return $this->client->searchByTerm($term);
    }

    public function pvzList($body): array
    {
        $body = [...$this->getCoordinates(), ...$body];
        return $this->client->getPvzList($body);
    }

    public function getCoordinates(): array
    {
        return [
            'latitude' => [
                'from' => $this->yMin,
                'to' => $this->yMax,
            ],
            'longitude' => [
                'from' => $this->xMin,
                'to' => $this->xMax,
            ],
        ];
    }
}
