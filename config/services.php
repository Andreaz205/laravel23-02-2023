<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'vkontakte' => [
        'client_id' => env('VK_CLIENT_ID'),
        'client_secret' => env('VK_CLIENT_SECRET'),
        'redirect' => env('VK_REDIRECT_URL'),
    ],
    'yookassa' => [
        'shop_id' => env('YOOKASSA_SHOP_ID', null),
        'secret_key' => env('YOOKASSA_SECRET_KEY', null),
        'return_url' => env('YOOKASSA_RETURN_URL', null),
    ],
    'cdek' => [
        'account' => env('SDK_ACCOUNT', null),
        'password' => env('SDK_PASSWORD', null),
    ],
    'business_lines' => [
        'appkey' => '410F6D1E-0903-4664-8EE2-FC005AD4C834',
        'login' => 'somonrahmatov828@gmail.com',
        'password' => 'winstoN2211',
    ],
];
