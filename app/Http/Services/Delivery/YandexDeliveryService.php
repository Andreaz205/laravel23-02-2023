<?php

namespace App\Http\Services\Delivery;

use App\Library\YandexDelivery\Client;

class YandexDeliveryService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function calculate($params)
    {
        return $this->client->calculatePrice($params);
    }

    public function getCoordinatesByTerm($term)
    {
        return $this->client->searchByTerm($term);
    }
}
