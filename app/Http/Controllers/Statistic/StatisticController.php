<?php

namespace App\Http\Controllers\Statistic;

use AkkiIo\LaravelGoogleAnalytics\Facades\LaravelGoogleAnalytics;
use AkkiIo\LaravelGoogleAnalytics\Period;
use App\Http\Controllers\Controller;
use Inertia\Response;

class StatisticController extends Controller
{
    public function index(): Response
    {
        $data = LaravelGoogleAnalytics::dateRanges(Period::days(1))
            ->metrics('screenPageViews')->dimensions('country', 'landingPage', 'date')->get();
        dd($data->metricAggregationsTable);
        return inertia('Statistic/Index');
    }
}
