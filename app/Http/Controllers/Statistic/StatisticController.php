<?php

namespace App\Http\Controllers\Statistic;

use AkkiIo\LaravelGoogleAnalytics\Facades\LaravelGoogleAnalytics;
use AkkiIo\LaravelGoogleAnalytics\Period;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        $data = LaravelGoogleAnalytics::dateRanges(Period::days(1))
            ->metrics('screenPageViews')->dimensions('country', 'landingPage', 'date')->get();
        dd($data->metricAggregationsTable);
        return inertia('Statistic/Index');
    }
}
