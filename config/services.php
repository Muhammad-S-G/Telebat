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


    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'payment_methods' => ['card']
    ],


    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'sandbox_email' => env('PAYPAL_SANDBOX_EMAIL'),
    ],

    'firebase' => [

        'credentials' => storage_path(env('FIREBASE_CREDENTIALS')),

        'project_id' => env('FIREBASE_PROJECT_ID'),

        'fcm_endpoint' => 'https://fcm.googleapis.com/v1/projects/'
            . env('FIREBASE_PROJECT_ID')
            . '/messages:send',


        'database' => [
            'default' => [
                'url' => env('FIREBASE_DATABASE_URL'),
            ],
        ],

        'storage' => [
            'default' => [
                'bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET'),
            ],
        ],
    ]
];
