<?php

namespace App\Http\Services\YooKassa;


class YooKassaService
{
    public static function getClient()
    {

    }

//    public static function weekPagesData()
//    {
//        $client = YooKassaService::getClient();
//        $response = $client->runReport([
//            'property' => 'properties/358181365',
//            'dateRanges' => [
//                new DateRange([
//                    'start_date' => '7daysAgo',
//                    'end_date' => 'today',
//                ]),
//            ],
//            'dimensions' => [
//                new Dimension([
//                        'name' => 'pageTitle'
//                    ]
//                )],
//            'metrics' => [
//                new Metric(
//                    [
//                        'name' => 'screenPageViews',
//                    ]),
////                new Metric([
////                    "name" => "eventCount"
////                ]),
//            ],
//
//        ]);
//
//        $map = [];
//        foreach ($response->getRows() as $row) {
//            foreach ($row->getDimensionValues() as $dimensionValue) {
//                $map['dimensions'][] =   $dimensionValue->getValue() ;
//            }
//            foreach($row->getMetricValues() as $metricValue) {
//                $map['metrics'][] =  $metricValue->getValue();
//            }
//        }
//        return $map;
//    }
//
//    public static function weekViewsData()
//    {
//        $client = YooKassaService::getClient();
//        $response = $client->runReport([
//            'property' => 'properties/358181365',
//            'dateRanges' => [
//                new DateRange([
//                    'start_date' => '7daysAgo',
//                    'end_date' => 'today',
//                ]),
//            ],
//            'dimensions' => [
//                new Dimension([
//                        'name' => 'date'
//                    ]
//                )],
//            'metrics' => [
//                new Metric(
//                    [
//                        'name' => 'totalUsers',
//                    ]),
////                new Metric([
////                    "name" => "eventCount"
////                ]),
//            ],
//            "orderBys" =>  [
//                new OrderBy([
//                    'dimension' => new OrderBy\DimensionOrderBy([
//                        "dimension_name" => "date"
//                    ])
//                ])
//            ],
//        ]);
//
//        $map = [];
//        foreach ($response->getRows() as $key => $row) {
//            foreach ($row->getDimensionValues() as $dimensionValue) {
//                $map[$key]['dimensions'] = $dimensionValue->getValue() ;
//            }
//            foreach($row->getMetricValues() as $metricValue) {
//                $map[$key]['metrics'] = $metricValue->getValue();
//            }
//        }
//        dd($map);
//        return $map;
//    }
//
//    /**
//     * @throws \Google\ApiCore\ApiException
//     */
//    public static function getMetadata()
//    {
//        $client = YooKassaService::getClient();
//        $propertyId = 0;
//        $formattedName = sprintf('properties/%s/metadata', $propertyId);
//        $response = $client->getMetadata($formattedName);
//        dd($response);
//    }
}
