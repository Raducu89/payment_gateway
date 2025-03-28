<?php

return [
    'default_provider' => env('PAYMENT_PROVIDER', 'stripe'),

    'providers' => [
        'stripe' => [
            'class' => \App\Services\Payment\StripePaymentProvider::class,
            'config' => [
                'key' => env('STRIPE_KEY'),
                'secret' => env('STRIPE_SECRET'),
                'secret_key' => env('STRIPE_SECRET'),
                'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
                'success_rate' => env('STRIPE_SUCCESS_RATE', 80),
            ],
        ],
    ],

    'webhook_secret' => [
        'paypal' =>  env('PAYMENT_WEBHOOK_SECRET', 'my_webhook_secret'),
        'stripe' =>  env('PAYMENT_WEBHOOK_SECRET', 'my_webhook_secret'),
    ],
];
