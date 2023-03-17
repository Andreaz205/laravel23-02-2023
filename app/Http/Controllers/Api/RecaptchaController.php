<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecaptchaController extends Controller
{
//    public function get()
//    {
//        $token = session('recaptcha-token');
//    }

    public function checkCaptcha(Request $request)
    {
        $secret = '6Ld6fQolAAAAAAGAziQRZJMXRg3RpmC2g2m8htkP';
        $token = session('recaptcha-token');
        $params = array(
            'secret' => $secret,
            'response' => $token,
        );

        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params)
        ));

        $response = curl_exec($myCurl);
        curl_close($myCurl);

        $response = json_decode($response, true);
        return $response;
    }

    public function storeToken(Request $request)
    {
        $token = $request->get('token');
        $request->session()->put('recaptcha-token', $token);
        return $request->get('token');
    }

    public function getSessionData(Request $request)
    {
        return $request->session()->all();
    }
}
