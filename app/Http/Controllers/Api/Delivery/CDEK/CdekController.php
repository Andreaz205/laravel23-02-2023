<?php

namespace App\Http\Controllers\Api\Delivery\CDEK;

use App\Http\Controllers\Controller;
use App\Http\Services\Delivery\CDEKService;
use Illuminate\Http\Request;


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

    public function getLocalities()
    {
        return $this->service->fetchLocalities();
    }

    public function calculateByAvailableTariffs(Request $request)
    {
        return $this->service->calculateByAvailableTariffs($request->all());
    }


}
