<?php

namespace App\Http\Controllers\Statistic;

use App\Http\Controllers\Controller;
use App\Http\Services\Analytics\AnalyticsService;
use Inertia\Response;

class StatisticController extends Controller
{
    public function index(AnalyticsService $analyticsService)
    {
        dd($analyticsService::getMetadata());
        $weekPagesData = $analyticsService::weekPagesData();
        $weekViewsData = $analyticsService::weekViewsData();

        return inertia('Statistic/Index', [
            'weekPagesData' => $weekPagesData,
            'weekViewsData' => $weekViewsData,
        ]);
    }
}
