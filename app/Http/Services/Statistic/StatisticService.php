<?php

namespace App\Http\Services\Statistic;

use App\Models\Order;

class StatisticService
{
    public function ordersCountChartData($period, \Illuminate\Database\Eloquent\Collection $orders, string $periodDetailing = 'day'): array
    {
        $ordersCountData = [];
        foreach ($period as $periodItemStart) {
            $count = 0;
            foreach ($orders as $order) {
                $orderCreatedAt = $order->created_at;
                $start = $periodItemStart->copy();

                $last = match ($periodDetailing) {
                    'week' => $periodItemStart->copy()->addWeek(),
                    'month' => $periodItemStart->copy()->addMonth(),
                    default => $periodItemStart->copy()->addDay(),
                };

                if ($orderCreatedAt >= $start && $orderCreatedAt < $last) {
                    $count++;
                }
            }
            $ordersCountData[] = $count;
        }
        $ordersDateLabels = [];
        foreach ($period as $item) {
            $ordersDateLabels[] = $item->format('Y-m-d');
        }
        return [
            'ordersCountData' => $ordersCountData,
            'ordersDateLabels' => $ordersDateLabels,
        ];
    }

    public function ordersProfitChartData($period, \Illuminate\Database\Eloquent\Collection $orders, string $periodDetailing = 'day'): array
    {
        $ordersProfitData = [];
        foreach ($period as $periodItemStart) {
            $profit = 0;
            foreach ($orders as $order) {
                $orderCreatedAt = $order->created_at;
                $start = $periodItemStart->copy();

                $last = match ($periodDetailing) {
                    'week' => $periodItemStart->copy()->addWeek(),
                    'month' => $periodItemStart->copy()->addMonth(),
                    default => $periodItemStart->copy()->addDay(),
                };

                if ($orderCreatedAt >= $start && $orderCreatedAt < $last && $order->is_payed) {
                    $profit += $order->sum - $order->purchase_sum;
                }
            }
            $ordersProfitData[] = $profit;
        }
        $ordersDateLabels = [];
        foreach ($period as $item) {
            $ordersDateLabels[] = $item->format('Y-m-d');
        }
        return [
            'ordersProfitData' => $ordersProfitData,
            'ordersDateLabels' => $ordersDateLabels,
        ];
    }
}
