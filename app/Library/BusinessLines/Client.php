<?php

namespace App\Library\BusinessLines;

use App\Models\ServiceTokens;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class Client
{
    private function getClient()
    {
        $token = ServiceTokens::query()->whereServiceName('business_lines')->first()?->token;
        $client = new \GuzzleHttp\Client();
        if (!isset($token)) {
            $this->refetchToken();
        }
        return $client;
    }

    private function refetchToken()
    {
        try {
            DB::beginTransaction();
            ServiceTokens::whereServiceName('business_lines')->delete();
            $client = new \GuzzleHttp\Client();
            $body = [
                'appkey' => config('services.business_lines.appkey'),
                'login' => config('services.business_lines.login'),
                'password' => config('services.business_lines.password'),
            ];
            $response = $client->post('https://api.dellin.ru/v3/auth/login.json', ['json' => $body]);
            $token = json_decode($response->getBody()->getContents())->data->sessionID;
            ServiceTokens::create([
                'token' => $token,
                'service_name' => 'business_lines'
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['message' => mb_convert_encoding($exception->getMessage(), "UTF-8", "auto")], 500);
        }
        return $token;
    }

    public function cities()
    {
        $client = $this->getClient();
        $body = ['appkey' => config('services.business_lines.appkey')];
        $response = $client->post('https://api.dellin.ru/v3/public/terminals.json', ['json' => $body]);
        $jsonResponse = json_decode($response->getBody()->getContents());
        $hash = $jsonResponse->hash;
        $url = $jsonResponse->url;
        $isFileExists = file_exists(storage_path('/app/delivery/business-lines/cities/' . $hash));
        try {
            if (!$isFileExists) {
                $resource = fopen(storage_path('/app/delivery/business-lines/cities/' . $hash), 'a+');
                $client->get($url, ['sink' => $resource]);
            }
            $jsonFile = fopen(storage_path('/app/delivery/business-lines/cities/' . $hash), 'r');
            $data = stream_get_contents($jsonFile);
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage()], 500);
        }
        return $data;
    }

    public function getCitiesByTerm($body)
    {
        $client = $this->getClient();
        $body = [
            ...$body,
            'appkey' => config('services.business_lines.appkey'),
            'limit' => 50,
        ];
        try {
            $response = $client->post('https://api.dellin.ru/v2/public/kladr.json', ['json' => $body]);
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage()], 500);
        }
        return $response->getBody()->getContents();
    }

    public function calculate($params)
    {

        $client = $this->getClient();
        $sessionId = ServiceTokens::whereServiceName('business_lines')->first()->token;
        $body = [
            'sessionID' => $sessionId,
            'appkey' => config('services.business_lines.appkey'),
            ...$params,

        ];

        try {
            $response = $client->post('https://api.dellin.ru/v2/calculator.json', ['json' => $body]);
        } catch (BadResponseException $exception) {
            $exceptionBody = json_decode($exception->getResponse()->getBody()->getContents());
            if ($exceptionBody->metadata->status === 400) return Response::json(['message' => $exceptionBody->errors], 400);
            return Response::json(['message' => mb_convert_encoding($exception->getMessage(), "UTF-8", "auto")], 500);
        }
        return $response->getBody()->getContents();
    }

    public function getStreet($params)
    {
        $client = $this->getClient();
        $body = [
            ...$params,
            'appkey' => config('services.business_lines.appkey'),
            'sessionID' => ServiceTokens::whereServiceName('business_lines')->first()->token
        ];
        try {
            $response = $client->post("https://api.dellin.ru/v1/public/kladr_street.json", ['json' => $body]);
        } catch (\Exception $exception) {
            $exceptionBody = json_decode($exception->getResponse()->getBody()->getContents());
            if ($exceptionBody->metadata->status === 400) return Response::json(['message' => $exceptionBody->errors], 400);
            return Response::json(['message' => mb_convert_encoding($exception->getMessage(), "UTF-8", "auto")], 500);
        }
        return $response->getBody()->getContents();
    }

    public function getTerminals($params)
    {
        $client = $this->getClient();
        $body = [
            ...$params,
            'appkey' => config('services.business_lines.appkey'),
            'sessionID' => ServiceTokens::whereServiceName('business_lines')->first()->token
        ];
        try {
            $response = $client->post("https://api.dellin.ru/v1/public/request_terminals.json", ['json' => $body]);
        } catch (\Exception $exception) {
            $exceptionBody = json_decode($exception->getResponse()->getBody()->getContents());
            if ($exceptionBody->metadata->status === 400) return Response::json(['message' => $exceptionBody->errors], 400);
            return Response::json(['message' => mb_convert_encoding($exception->getMessage(), "UTF-8", "auto")], 500);
        }
        return $response->getBody()->getContents();
    }
}
