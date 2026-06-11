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

    'tech_discovery' => [
        'schedule_time' => env('TECH_DISCOVERY_CRAWL_TIME', '05:00'),
        'timeout_seconds' => env('TECH_DISCOVERY_TIMEOUT', 15),
        'retry_times' => env('TECH_DISCOVERY_RETRY', 1),
        'user_agent' => env('TECH_DISCOVERY_USER_AGENT', 'TechSavvyBot/1.0'),
        'github_token' => env('GITHUB_TOKEN'),
        'feed_limit_per_source' => env('TECH_DISCOVERY_FEED_LIMIT', 10),
        'providers' => [
            'github_trending' => [
                'url' => env('GITHUB_TRENDING_URL', 'https://github.com/trending?since=daily'),
                'limit' => env('GITHUB_TRENDING_LIMIT', 15),
            ],
        ],
        'feeds' => [
            'github_blog' => [
                'url' => 'https://github.blog/feed/',
            ],
            'github_changelog' => [
                'url' => 'https://github.blog/changelog/feed/',
            ],
            'infoq' => [
                'url' => 'https://feed.infoq.com/',
            ],
            'hacker_news_frontpage' => [
                'url' => 'https://hnrss.org/frontpage',
            ],
            'dev_to' => [
                'url' => 'https://dev.to/feed',
            ],
            'arxiv_ai' => [
                'url' => 'https://rss.arxiv.org/rss/cs.AI',
            ],
            'arxiv_machine_learning' => [
                'url' => 'https://rss.arxiv.org/rss/cs.LG',
            ],
            'arxiv_computation_language' => [
                'url' => 'https://rss.arxiv.org/rss/cs.CL',
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
