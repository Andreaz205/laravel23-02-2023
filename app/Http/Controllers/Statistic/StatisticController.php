<?php

namespace App\Http\Controllers\Statistic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistic\StatisticRequest;
use App\Http\Services\Analytics\AnalyticsService;
use App\Models\Order;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class StatisticController extends Controller
{
    public function index(AnalyticsService $analyticsService, StatisticRequest $request)
    {
        // для detailing = days
        $data = $request->validated();
        $from = isset($data['from']) ? Carbon::parse($data['from']) : Carbon::now()->subDays(30)->startOfDay();
        $to = isset($data['to']) ? Carbon::parse($data['to']):  Carbon::now();
        $period = CarbonPeriod::since($from)->days(1)->until($to)->toArray();


//        dd($period);
//        if ($from > $to) $dateLabels = null;
//        else {
//            $currentDay = $data['from'];
//            $lastDay = $data['to'];
//            while($currentDay <= $lastDay) {
//                $dateLabels[] = $currentDay->format();
//            }
//        }

//        dd($analyticsService::getMetadata());
//        $weekPagesData = $analyticsService::weekPagesData();
//        dd($weekPagesData);
//        $weekViewsData = $analyticsService::weekViewsData();

//        return inertia('Statistic/Index', [
//            'weekPagesData' => $weekPagesData,
//            'weekViewsData' => $weekViewsData,
//        ]);

        $orders = Order::query()->whereBetween('created_at', [$from, $to])->get();
        $ordersCountData = [];
        foreach ($period as $periodItemStart) {
            $count = 0;
            foreach ($orders as $order) {
                $orderCreatedAt = $order->created_at;
                $start = $periodItemStart->copy();
                $last = $periodItemStart->copy()->addDay();
                if ($orderCreatedAt >= $start && $orderCreatedAt < $last) {
                   $count++;
                }
            }
            $ordersCountData[] = $count;
        }
        $ordersDateLabels = [];
        foreach ($period as $item) {
            $ordersDateLabels[] = $item->format('d-m-Y');
        }


        return inertia('Statistic/Index', [
            'ordersDateLabels' => $ordersDateLabels,
            'ordersCountData' => $ordersCountData,
//            'orders' => $orders,
//            'weekPagesData' => $weekPagesData,
//            'weekViewsData' => $weekViewsData,
        ]);
    }
}
