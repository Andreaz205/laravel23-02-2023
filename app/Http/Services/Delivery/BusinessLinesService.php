<?php

namespace App\Http\Services\Delivery;

use App\Library\BusinessLines\Client;
use Illuminate\Http\Request;

class BusinessLinesService
{

    public function __construct(Client $businessLinesClient)
    {
        $this->businessLinesClient = $businessLinesClient;
    }
    public function cities()
    {
         return $this->businessLinesClient->cities();
    }

    public function getCitiesByTerm($body)
    {
        return $this->businessLinesClient->getCitiesByTerm($body);
    }

    public function calculate($params)
    {
        return $this->businessLinesClient->calculate($params);
    }

    public function getStreet($params)
    {
        return $this->businessLinesClient->getStreet($params);
    }

    public function getTerminals($params)
    {
        return $this->businessLinesClient->getTerminals($params);
    }
}
