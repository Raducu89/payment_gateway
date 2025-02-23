<?php

return [
    'stripe' => [
        'base_uri' => env('STRIPE_BASE_URI', 'https://api.stripe.com'),
        'key' => env('STRIPE_KEY', 'test'),
        'secret' => env('STRIPE_SECRET', 'test'),
    ],
    'paypal' => [
        'base_uri' => env('PAYPAL_BASE_URI', 'https://api.paypal.com'),
        'client_id' => env('PAYPAL_CLIENT_ID', 'test'),
        'secret' => env('PAYPAL_SECRET', 'test'),
    ],
];