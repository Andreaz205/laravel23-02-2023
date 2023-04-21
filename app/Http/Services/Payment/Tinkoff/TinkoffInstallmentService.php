<?php

namespace App\Http\Services\Payment\Tinkoff;

class TinkoffInstallmentService
{
    private function getClient()
    {
//        $token = config('services.yandex-delivery.token');
        return new \GuzzleHttp\Client([
            'headers' => [
                'Accept-Language' => 'ru',
//                'Authorization' => 'Bearer '.$token,
            ]
        ]);
    }
}
