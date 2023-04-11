<?php

namespace App\Http\Services\Analytics;

use Carbon\Carbon;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;

class AnalyticsService
{
    public static function getClient()
    {
        return new BetaAnalyticsDataClient([
            'credentials' => storage_path('/app/analytics/service-account-credentials.json')
        ]);
    }

    public static function monthDevicesData($from = null, $to = null)
    {
        // $from = 2023-03-02 например

        $from = Carbon::now()->subDays(30)->startOfDay()->format('Y-m-d');
        $to = Carbon::now()->endOfDay()->format('Y-m-d');

        $client = AnalyticsService::getClient();
        $response = $client->runReport([
            'property' => 'properties/358181365',
            'dateRanges' => [
                new DateRange([
                    'start_date' => $from,
                    'end_date' => $to,
                ]),
            ],
            'dimensions' => [
//                new Dimension([
//                        'name' => 'pageTitle'
//                    ]
//                )],
                new Dimension([
                        'name' => 'platformDeviceCategory'
                    ]
                )],
            'metrics' => [
//                new Metric(
//                    [
//                       'name' => 'screenPageViews',
//                       'name' => 'eventValue',
//                    ]
//                ),
                new Metric([
                    "name" => "eventCount"
                ]),
            ],
        ]);
        $map = [];
        foreach ($response->getRows() as $row) {
            foreach ($row->getDimensionValues() as $dimensionValue) {
                $map['labels'][] =   $dimensionValue->getValue() ;
            }
            foreach($row->getMetricValues() as $metricValue) {
                $map['count'][] =  $metricValue->getValue();
            }
        }
        return $map;
    }

    public static function monthViewsData($from = null, $to = null)
    {
        // $from = 2023-03-02 например
        // $from = 2023-03-08 например
        if (!isset($from)) {
            $from = Carbon::now()->subDays(30)->format('Y-m-d');
        }
        if (!isset($to)) {
            $to = Carbon::now()->format('Y-m-d');
        }

        $client = AnalyticsService::getClient();
        $response = $client->runReport([
            'property' => 'properties/358181365',
            'dateRanges' => [
                new DateRange([
                    'start_date' => $from,
                    'end_date' => $to,
                ]),
            ],
            'dimensions' => [
                new Dimension([
                        'name' => 'date'
                    ]
                )],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'screenPageViews',
                    ]),
//                new Metric([
//                    "name" => "eventCount"
//                ]),
            ],
            "orderBys" =>  [
                new OrderBy([
                    'dimension' => new OrderBy\DimensionOrderBy([
                        "dimension_name" => "date"
                    ])
                ])
            ],
        ]);


        $labels = [];
        $countVisits = [];

        foreach ($response->getRows() as $key => $row) {
            foreach ($row->getDimensionValues() as $dimensionValue) {
                $date = $dimensionValue->getValue();
                $year = '';
                for ($i = 0; $i < 4; $i++) {
                    $year .= $date[$i];
                }

                $month = '';
                for ($i = 4; $i < 6; $i++) {
                    $month .= $date[$i];
                }

                $day = '';
                for ($i = 6; $i < 8; $i++) {
                    $day .= $date[$i];
                }

                $formattedDate = $day . '-' . $month . '-' . $year;
                $labels[] = $formattedDate;
            }
            foreach($row->getMetricValues() as $metricValue) {
                $countVisits[] = $metricValue->getValue();
            }
        }
        return [
            'labels' => $labels,
            'count' => $countVisits,
        ];
    }

    public static function monthVisitsPerPage()
    {
        $from = Carbon::now()->subDays(30)->startOfDay()->format('Y-m-d');
        $to = Carbon::now()->endOfDay()->format('Y-m-d');

        $client = AnalyticsService::getClient();
        $response = $client->runReport([
            'property' => 'properties/358181365',
            'dateRanges' => [
                new DateRange([
                    'start_date' => $from,
                    'end_date' => $to,
                ]),
            ],
            'dimensions' => [
                new Dimension([
                        'name' => 'pageTitle'
                    ]
                )],
            'metrics' => [
                new Metric([
                    "name" => "screenPageViews"
                ]),
            ],
        ]);
//        $pages = [];
//        $visitsCount = [];
        $map = [];
        foreach ($response->getRows() as $key => $row) {
            foreach ($row->getDimensionValues() as $dimensionValue) {
                $map[$key][] =  $dimensionValue->getValue();
            }
            foreach($row->getMetricValues() as $metricValue) {
                $map[$key][] =  $metricValue->getValue();
            }
        }

//        $map = [];
//        foreach ($pages as $key => $page) {
//            foreach ($visitsCount as $visitKey=>$count) {
//                if ($key === $visitKey) {
//                    $map[$page] = $count;
//                    break;
//                }
//            }
//        }
        return $map;
    }



    /**
     * @throws \Google\ApiCore\ApiException
     */
    public static function getMetadata()
    {
        $client = AnalyticsService::getClient();
        $propertyId = 0;
        $formattedName = sprintf('properties/%s/metadata', $propertyId);
        $response = $client->getMetadata($formattedName);
        dd($response);
    }
}
