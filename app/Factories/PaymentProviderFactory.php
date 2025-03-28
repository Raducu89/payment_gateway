<?php

namespace App\Factories;

use App\Services\Payment\PaymentProviderInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

class PaymentProviderFactory
{
    /**
     * Create an instance of a payment provider based on the given key.
     *
     * @param string|null $providerKey
     * @return PaymentProviderInterface
     * @throws \Exception
     */
    public static function make(?string $providerKey = null): PaymentProviderInterface
    {
        $providerKey = $providerKey ?? config('payment.default_provider');
        $providers = config('payment.providers');

        if (!is_array($providers) || !isset($providers[$providerKey])) {
            throw new \InvalidArgumentException("Payment provider not found: {$providerKey}");
        }

        $providerData = $providers[$providerKey];

        if (!isset($providerData['class']) || !is_string($providerData['class'])) {
            throw new \UnexpectedValueException("Class not defined or invalid for provider: {$providerKey}");
        }

        $class = $providerData['class'];

        try {
            return app()->make($class, [
                'config' => $providerData['config'] ?? [],
            ]);
        } catch (BindingResolutionException $e) {
            throw new \RuntimeException("Failed to resolve payment provider: {$providerKey}", 0, $e);
        }
    }
}
