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
                'name' => env('MARKET_SOURCE_BITCOIN_NAME', 'coingecko'),
                'api_url' => env('MARKET_SOURCE_BITCOIN_API_URL', 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd'),
                'scrape_url' => env('MARKET_SOURCE_BITCOIN_SCRAPE_URL'),
            ],
            'world_gold' => [
                'name' => env('MARKET_SOURCE_WORLD_GOLD_NAME', 'metals_api'),
                'api_url' => env('MARKET_SOURCE_WORLD_GOLD_API_URL'),
                'scrape_url' => env('MARKET_SOURCE_WORLD_GOLD_SCRAPE_URL'),
            ],
            'vn_gold' => [
                'name' => env('MARKET_SOURCE_VN_GOLD_NAME', 'vn_gold_source'),
                'api_url' => env('MARKET_SOURCE_VN_GOLD_API_URL'),
                'scrape_url' => env('MARKET_SOURCE_VN_GOLD_SCRAPE_URL'),
            ],
            'silver' => [
                'name' => env('MARKET_SOURCE_SILVER_NAME', 'metals_api'),
                'api_url' => env('MARKET_SOURCE_SILVER_API_URL'),
                'scrape_url' => env('MARKET_SOURCE_SILVER_SCRAPE_URL'),
            ],
            'fuel' => [
                'name' => env('MARKET_SOURCE_FUEL_NAME', 'vn_fuel_source'),
                'api_url' => env('MARKET_SOURCE_FUEL_API_URL'),
                'scrape_url' => env('MARKET_SOURCE_FUEL_SCRAPE_URL'),
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
