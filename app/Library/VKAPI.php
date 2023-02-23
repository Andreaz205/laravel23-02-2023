<?php

namespace App\Library;

class VKAPI
{
    private $v = '5.130';
    private $ownerId;
    private $token;

    public function __construct($ownerId, $token)
    {
        $this->ownerId = $ownerId;
        $this->token = $token;
    }

    public function getPosts($count)
    {
        $data = [
            'owner_id' => $this->ownerId,
            'count' => $count,
            'v' => $this->v,
            'access_token' => $this->token
        ];
        return $this->apiRequest('wall.get', $data);
    }

    private function apiRequest($method, $data = array())
    {
        $string = http_build_query($data);
        $url = 'https://api.vk.com/method/' . $method . '?';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . urldecode($string));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
