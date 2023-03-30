<?php

namespace App\Library\CDEK;

use App\Models\ServiceTokens;

class Client
{

    public function getClient()
    {
        $token = ServiceTokens::query()->whereServiceName('cdek')->first()?->token;
        $client = new \GuzzleHttp\Client();
        if (isset($token)) {
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ]
            ]);
    //            'grant_type' => 'client_credentials',
    //            'client_id' => config('services.cdek.account'),
    //            'client_secret' => config('services.cdek.password')
        } else {
            $client = new \GuzzleHttp\Client();
            $response = $client->post( 'https://api.cdek.ru/v2/oauth/token?grant_type=client_credentials&client_id=' . config('services.cdek.account') . '&client_secret=' . config('services.cdek.password'));
            $token = json_decode($response->getBody()->getContents())->access_token;
            ServiceTokens::whereServiceName('cdek')->delete();
            ServiceTokens::create([
                'service_name' => 'cdek',
                'token' => $token,
            ]);
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ]
            ]);
        }
        return $client;
    }

    public function fetchRegions()
    {
        try {
            $client = $this->getClient();
            $response = $client->get( 'https://api.cdek.ru/v2/location/regions');
            $regions = json_decode($response->getBody()->getContents());
            $map = [];
            foreach ($regions as $region) {
//                dump($region);
                $map[] = $region->country . ' ' . $region->region;
            }
            return $map;
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }


    }
}
