<?php

return [
    'newsapi' => [
        'base_url' => env('NEWSAPI_BASE_URL', 'https://newsapi.org/v2/'),
        'api_key' => env('NEWSAPI_API_KEY')
    ],
    'guardian' => [
        'base_url' => env('GUARDIAN_BASE_URL', 'https://content.guardianapis.com/'),
        'api_key' => env('GUARDIAN_API_KEY')
    ],
    'newyorktimes' => [
        'base_url' => env('NEWYORKTIMES_BASE_URL', 'https://api.nytimes.com/svc/news/v3/'),
        'api_key' => env('NEWYORKTIMES_API_KEY')
    ],
];
