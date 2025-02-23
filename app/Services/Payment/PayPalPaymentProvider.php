<?php

namespace App\Services\Payment;

use App\Models\Order;

class PayPalPaymentProvider implements PaymentProviderInterface
{
    public function pay(Order $order): array
    {
        // Simulare request către PayPal
        // ...
        return [
            'status' => 'success',
            'response' => [
                'transaction_id' => 'PAYPAL_TX_54321',
                'details' => 'PayPal payment simulation',
            ],
        ];
    }
}
