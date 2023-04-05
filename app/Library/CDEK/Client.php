<?php

namespace App\Library\CDEK;

use App\Models\ServiceTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class Client
{

    private function getClient()
    {
        $token = ServiceTokens::query()->whereServiceName('cdek')->first()?->token;
        if (!isset($token)) {
            $client = new \GuzzleHttp\Client();
            try {
                $response = $client->post('https://api.cdek.ru/v2/oauth/token?grant_type=client_credentials&client_id=' . config('services.cdek.account') . '&client_secret=' . config('services.cdek.password'));
                DB::beginTransaction();

                $token = json_decode($response->getBody()->getContents())->access_token;
                ServiceTokens::whereServiceName('cdek')->delete();
                ServiceTokens::create([
                    'service_name' => 'cdek',
                    'token' => $token,
                ]);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                return Response::json(['message' => $exception->getMessage()], 500);
            }
        }
        return new \GuzzleHttp\Client([
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ]
        ]);
    }

    private function refetchToken()
    {
        try {
            DB::beginTransaction();
            ServiceTokens::whereServiceName('cdek')->delete();
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://api.cdek.ru/v2/oauth/token?grant_type=client_credentials&client_id=' . config('services.cdek.account') . '&client_secret=' . config('services.cdek.password'));
            $token = json_decode($response->getBody()->getContents())->access_token;
            ServiceTokens::create([
                'token' => $token,
                'service_name' => 'cdek'
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['message' => $exception->getMessage()], 500);
        }
        return $token;
    }

    public function fetchRussianRegions()
    {
        try {
            $client = $this->getClient();
            $response = $client->get( 'https://api.cdek.ru/v2/location/regions?country_codes=RU');
            return json_decode($response->getBody()->getContents());
        } catch (\Exception $exception) {
            if ((int)$exception->getCode() === 401) {
                $this->refetchToken();
                return $this->fetchRussianRegions();
            }
            return Response::json(['message' => $exception->getMessage()], 500);
        }
    }

    public function fetchRussianLocalities()
    {
        try {
            $client = $this->getClient();
            $response = $client->get( 'https://api.cdek.ru/v2/location/cities?country_codes=RU');
            return json_decode($response->getBody()->getContents());
        } catch (\Exception $exception) {
            if ((int)$exception->getCode() === 401) {
                $this->refetchToken();
                return $this->fetchRussianLocalities();
            }
            return Response::json(['message' => $exception->getMessage()], 500);
        }
    }

    public function calculateByAvailableTariffs($body)
    {
        try {
            $client = $this->getClient();
            $response = $client->post( 'https://api.cdek.ru/v2/calculator/tarifflist', ['json' => $body]);
            return json_decode($response->getBody()->getContents());
        } catch (\Exception $exception) {
            if ((int)$exception->getCode() === 401) {
                $this->refetchToken();
                return $this->calculateByAvailableTariffs($body);
            }
            return Response::json(['message' => $exception->getMessage()], 500);
        }
    }

}
