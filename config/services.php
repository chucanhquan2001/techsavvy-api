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

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'contact_notification' => [
        'recipient_email' => env('CONTACT_NOTIFICATION_EMAIL'),
        'recipient_name' => env('CONTACT_NOTIFICATION_NAME', 'Admin'),
    ],

    'market_data' => [
        'schedule_time' => env('MARKET_CRAWL_TIME', '07:00'),
        'timeout_seconds' => env('MARKET_CRAWL_TIMEOUT', 10),
        'retry_times' => env('MARKET_CRAWL_RETRY', 1),
        'providers' => [
            'bitcoin' => [
                'name' => 'coingecko',
                'api_url' => 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd',
                'scrape_url' => null,
            ],
            'world_gold' => [
                'name' => 'metals_api',
                'api_url' => 'https://metals-api.com/api/latest?access_key=YOUR_METALS_API_KEY&base=USD&symbols=XAU',
                'scrape_url' => null,
            ],
            'vn_gold' => [
                'name' => 'vang_today',
                'api_url' => 'https://www.vang.today/api/prices?type=SJL1L10',
                'scrape_url' => null,
            ],
            'silver' => [
                'name' => 'metals_api',
                'api_url' => 'https://metals-api.com/api/latest?access_key=YOUR_METALS_API_KEY&base=USD&symbols=XAG',
                'scrape_url' => null,
            ],
            'fuel' => [
                'name' => 'pvoil',
                'api_url' => null,
                'scrape_url' => 'https://www.pvoil.com.vn/en/petroleum-retail-price',
            ],
        ],
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
