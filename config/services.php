<?php

return [

    'speedyindex' => [
'api_key' => env('SPEEDYINDEX_API_KEY'),
'base_url' => env('SPEEDYINDEX_BASE_URL', 'https://api.speedyindex.com/v2'),
'timeout' => env('SPEEDYINDEX_TIMEOUT', 30),
'retry_times' => env('SPEEDYINDEX_RETRY_TIMES', 3),
'retry_delay' => env('SPEEDYINDEX_RETRY_DELAY', 1000),
],


    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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
