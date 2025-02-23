<?php

namespace App\Factories;

use App\Services\Payment\PaymentProviderInterface;

class PaymentProviderFactory
{
    /**
     * Create an instance of a payment provider based on the given key.
     *
     * @param string $providerKey
     * @return PaymentProviderInterface
     * @throws \Exception
     */
    public static function make(string $providerKey): PaymentProviderInterface
    {
        // Retrieve the providers array from config
        $providers = config('payment.providers');

        // If the key is not found, throw an exception
        if (!isset($providers[$providerKey])) {
            throw new \Exception("Payment provider not found: {$providerKey}");
        }

        $providerClass = $providers[$providerKey];

        // Use the Laravel container to instantiate the provider (injecting dependencies if needed)
        return app($providerClass);
    }
}
