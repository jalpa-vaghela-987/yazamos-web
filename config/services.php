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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'sms' => [
        'api_send_url' => 'https://rest.nexmo.com/sms/json',
        'vonage_api_key' => '8a84d8a8',
        'vonage_api_secret' => 'VFRgrg^$3p',
        'vonage_from' => 'YAZAMOS'
    ],

    'tranzila' => [
        'supplier'       => env('TRANZILA_SUPPLIER'),
        'terminal'       => env('TRANZILA_TERMINAL'),
        'password'       => env('TRANZILA_PASSWORD'),
        'currency'       => env('TRANZILA_CURRENCY', 'USD'), //For testing purpose we used ILS currency and production stage we will use USD currency 
    ],

    'firebase' => [
        'server_key' => env('FIREBASE_SERVER_KEY'),
    ],

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

];
