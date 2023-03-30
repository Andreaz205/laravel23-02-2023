<?php

namespace App\Http\Services\Delivery;

use App\Library\CDEK\Client;

class CDEKService
{
    public function __construct(Client $cdekClient)
    {
        $this->cdekClient = $cdekClient;
    }

    public function fetchRegions()
    {
        return $this->cdekClient->fetchRegions();
    }
}
