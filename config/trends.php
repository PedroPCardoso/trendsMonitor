<?php

return [
    'cache_ttl' => env('TRENDS_CACHE_TTL', 3600),

    'youtube' => [
        'api_key' => env('YOUTUBE_API_KEY'),
        'region_code' => env('YOUTUBE_REGION_CODE', 'BR'),
        'max_results' => 10,
    ],

    'google' => [
        'provider' => env('GOOGLE_TRENDS_PROVIDER', 'pytrends'),
        'region' => 'BR',
        'hl' => 'pt-BR',
    ],

    'tiktok' => [
        'provider' => env('TIKTOK_PROVIDER', 'scraper'),
        'scraper_path' => base_path('python/tiktok_trends.py'),
    ],

    'instagram' => [
        'provider' => env('INSTAGRAM_PROVIDER', 'scraper'),
        'scraper_path' => base_path('python/instagram_trends.py'),
        'hashtags' => [
            'explorar', 'viral', 'reels', 'brasil', 'trends'
        ],
    ],
];
