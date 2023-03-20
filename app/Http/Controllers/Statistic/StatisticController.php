<?php

namespace App\Http\Controllers\Statistic;


use App\Http\Controllers\Controller;
use App\Http\Services\Analytics\AnalyticsService;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    /**
     * @throws \Google\ApiCore\ValidationException
     * @throws \Google\ApiCore\ApiException
     */
    public function index(AnalyticsService $analyticsService)
    {
        $weekPagesData = $analyticsService::weekPagesData();
        $weekViewsData = $analyticsService::weekViewsData();

        return inertia('Statistic/Index', [
            'weekPagesData' => $weekPagesData,
            'weekViewsData' => $weekViewsData,
        ]);
    }
}
