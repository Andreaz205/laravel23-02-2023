<?php

namespace App\Http\Services\Payment;


use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;
use YooKassa\Request\Payments\PaymentResponse;

class YooKassaService
{
    public function getClient()
    {
        $client = new Client();
        $client->setAuth(config('services.yookassa.shop_id'), config('services.yookassa.secret_key'));
        return $client;
    }

    /**
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function createPayment(float $amount, array $options= [])
    {
        $client = $this->getClient();
        $payment = $client->createPayment([
            'amount' => [
                'value' => $amount,
                'currency' => 'RUB',
            ],
            'capture' => false,
            'description' => '',
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => config('services.yookassa.return_url')
            ],
            'metadata' => [
                'transaction_id' => $options['transaction_id'],
            ],
        ], uniqid('', true));

        return $payment->getConfirmation()->getConfirmationUrl();
    }

    /**
     * @throws \Exception
     */
    private function getPaymentInfo(string $paymentId): PaymentResponse
    {
        $client = $this->getClient();
        try {
            return $client->getPaymentInfo($paymentId);
        } catch (\Exception $exception) {
             Log::error($exception);
             throw new \Exception('Поддельный запрос yoo kassa!');
        }
    }

    /**
     * @throws \Exception
     */
    public function checkStatus($payment)
    {
        $info = $this->getPaymentInfo($payment->id);
        $status = $info->getStatus();
        if (! $payment->status === $status) {
            throw new \Exception('Поддельный запрос!');
        }
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
