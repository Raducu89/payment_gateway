<?php

return [
    'default_provider' => env('PAYMENT_PROVIDER', 'stripe'),

    'providers' => [
        'stripe' => \App\Services\Payment\StripePaymentProvider::class,
        'paypal' => \App\Services\Payment\PayPalPaymentProvider::class,
        //'braintree' => \App\Services\Payment\BraintreePaymentProvider::class,
    ],

    'webhook_secret' => [
        'paypal' =>  env('PAYMENT_WEBHOOK_SECRET', 'my_webhook_secret'),
        'stripe' =>  env('PAYMENT_WEBHOOK_SECRET', 'my_webhook_secret'),
    ],  
];
