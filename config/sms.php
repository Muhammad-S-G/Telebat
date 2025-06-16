<?php
return [
    'gateway' => [
        'url' => env('SMS_GATEWAY_URL', 'https://www.traccar.org/sms'),
        'api_key' => env('SMS_GATEWAY_APIKEY'),
    ]
];
