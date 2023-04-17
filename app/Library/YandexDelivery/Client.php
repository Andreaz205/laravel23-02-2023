<?php

namespace App\Library\YandexDelivery;

use GuzzleHttp\Exception\BadResponseException;
use Inertia\Response;

class Client
{
    private function getClient()
    {
        $token = config('services.yandex-delivery.token');
        return new \GuzzleHttp\Client([
            'headers' => [
                'Accept-Language' => 'ru',
                'Authorization' => 'Bearer '.$token,
            ]
        ]);
    }

    public function calculatePrice($params)
    {
        $client = $this->getClient();
        $body = [...$params];
        $idempotency_token = 123456789;
        try {
            $response = $client->post('https://b2b-authproxy.taxi.yandex.net/api/b2b/platform/offers/create', ['json' => $body]);
//            $response = $client->post('https://b2b.taxi.yandex.net/b2b/cargo/integration/v2/check-price', ['json' => $body]);
        } catch (BadResponseException $exception) {
            dd(json_decode($exception->getResponse()->getBody()->getContents()));
            $message = json_decode($exception->getResponse()->getBody()->getContents())->message;
            return \Illuminate\Support\Facades\Response::json(['message' => $message], 400);
        }
        return json_decode($response->getBody()->getContents());

    }

    public function searchByTerm($term)
    {
        $client = $this->getClient();

        try {
            $response = $client->get('https://geocode-maps.yandex.ru/1.x/?format=json&apikey='. config('services.yandex-maps.geocoder_token') .'&geocode=' . $term);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
        return $response->getBody()->getContents();
    }

    public function getPvzList($body)
    {
        $client = $this->getClient();
        try {
            $response = $client->post('https://b2b-authproxy.taxi.yandex.net/api/b2b/platform/pickup-points/list', ['json' => $body]);
        } catch (\Exception $exception) {
            $response = json_decode($exception->getResponse()->getBody()->getContents());
            $errorMessage = $response?->error_details ?? 'Что то пошло не так!';
            return \Illuminate\Support\Facades\Response::json(['message' => $errorMessage], 500);
        }
        $pointsData = $response->getBody()->getContents();
        return $this->convertPvzList($pointsData);
    }

    private function convertPvzList(string $pointsData): array
    {
        $pointsDataJson = json_decode($pointsData);
        $points = $pointsDataJson->points;
        $map = [];
        foreach ($points as $point) {
            $map[] = [
                'platform_id' => $point->id,
                'address' => $point->address->full_address
            ];
        }
        return $map;
    }
}
