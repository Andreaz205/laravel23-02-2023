<?php

namespace App\Http\Controllers\Statistic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistic\StatisticRequest;
use App\Http\Services\Analytics\AnalyticsService;
use App\Http\Services\Statistic\StatisticService;
use App\Models\Order;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

class StatisticController extends Controller
{
    private StatisticService $service;

    public function __construct(StatisticService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        // для detailing = days

        $from = Carbon::now()->subDays(30)->startOfDay();
        $to = Carbon::now()->endOfDay();
        $period = CarbonPeriod::since($from)->days(1)->until($to)->toArray();

        $orders = Order::query()->whereBetween('created_at', [$from, $to])->get();

        try {
            $viewsData = AnalyticsService::monthViewsData($from->format('Y-m-d'), $to->format('Y-m-d'));
            $devicesData = AnalyticsService::monthDevicesData();
            $visitsPerPage = AnalyticsService::monthVisitsPerPage();
        } catch (\Exception $exception) {
            return 'Some error occurred!';
        }

        $ordersCountChartData = $this->service->ordersCountChartData($period, $orders);

        $ordersProfitChartData = $this->service->ordersProfitChartData($period, $orders);
        return inertia('Statistic/Index', [
            'ordersCountChartData' => $ordersCountChartData,
            'ordersProfitChartData' => $ordersProfitChartData,
            'pagesVisits' => $viewsData,
            'devicesData' => $devicesData,
            'visitsPerPage' => $visitsPerPage
        ]);
    }

    public function calculateOrdersCountPeriod(StatisticRequest $request): JsonResponse
    {
        $data = $request->validated();

        $from = isset($data['from']) ? Carbon::createFromTimeString($data['from'])->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $to = isset($data['to']) ? Carbon::createFromTimeString($data['to'])->endOfDay() :  Carbon::now()->endOfDay();
        $detailing = $data['detailing'];

        $orders = Order::query()->whereBetween('created_at', [$from, $to])->get();

        $period = match ($detailing) {
            'week' => CarbonPeriod::since($from)->weeks(1)->until($to)->toArray(),
            'month' => CarbonPeriod::since($from)->months(1)->until($to)->toArray(),
            default => CarbonPeriod::since($from)->days(1)->until($to)->toArray(),
        };

        $ordersCountChartData = $this->service->ordersCountChartData($period, $orders, $detailing);
        return Response::json($ordersCountChartData);
    }

    public function calculateOrdersProfitPeriod(StatisticRequest $request): JsonResponse
    {
        $data = $request->validated();

        $from = isset($data['from']) ? Carbon::createFromTimeString($data['from'])->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $to = isset($data['to']) ? Carbon::createFromTimeString($data['to'])->endOfDay() :  Carbon::now()->endOfDay();
        $detailing = $data['detailing'];

        $orders = Order::query()->whereBetween('created_at', [$from, $to])->get();

        $period = match ($detailing) {
            'week' => CarbonPeriod::since($from)->weeks(1)->until($to)->toArray(),
            'month' => CarbonPeriod::since($from)->months(1)->until($to)->toArray(),
            default => CarbonPeriod::since($from)->days(1)->until($to)->toArray(),
        };

        $ordersProfitChartData = $this->service->ordersProfitChartData($period, $orders, $detailing);
        return Response::json($ordersProfitChartData);
    }


}
