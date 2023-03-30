<?php

namespace App\Http\Controllers\Api\Delivery\CDEK;

use App\Http\Controllers\Controller;
use App\Http\Services\Delivery\CDEKService;

class CdekController extends Controller
{
    public function __construct(CDEKService $service)
    {
        $this->service = $service;
    }

    public function getRegions()
    {
        return $this->service->fetchRegions();
    }


}
