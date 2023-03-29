<?php

namespace App\Http\Services\Analytics;

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

    public static function weekPagesData()
    {
        $client = AnalyticsService::getClient();
        $response = $client->runReport([
            'property' => 'properties/358181365',
            'dateRanges' => [
                new DateRange([
                    'start_date' => '7daysAgo',
                    'end_date' => 'today',
                ]),
            ],
            'dimensions' => [
                new Dimension([
                        'name' => 'pageTitle'
                    ]
                )],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'screenPageViews',
                    ]
                ),
//                new Metric([
//                    "name" => "eventCount"
//                ]),
            ],

        ]);

        $map = [];
        foreach ($response->getRows() as $row) {
            foreach ($row->getDimensionValues() as $dimensionValue) {
                $map['dimensions'][] =   $dimensionValue->getValue() ;
            }
            foreach($row->getMetricValues() as $metricValue) {
                $map['metrics'][] =  $metricValue->getValue();
            }
        }
        return $map;
    }

    public static function weekViewsData()
    {
        $client = AnalyticsService::getClient();
        $response = $client->runReport([
            'property' => 'properties/358181365',
            'dateRanges' => [
                new DateRange([
                    'start_date' => '7daysAgo',
                    'end_date' => 'today',
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
                        'name' => 'totalUsers',
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

        $map = [];
        foreach ($response->getRows() as $key => $row) {
            foreach ($row->getDimensionValues() as $dimensionValue) {
                $map[$key]['dimensions'] = $dimensionValue->getValue() ;
            }
            foreach($row->getMetricValues() as $metricValue) {
                $map[$key]['metrics'] = $metricValue->getValue();
            }
        }
        dd($map);
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
