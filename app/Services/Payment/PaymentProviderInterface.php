<?php

namespace App\Services\Payment;

use App\Models\Order;

interface PaymentProviderInterface
{
    /**
     * Procesează plata pentru o comandă.
     *
     * @param Order $order
     * @return array - date despre rezultat (status, response, etc.)
     */
    public function pay(Order $order): array;
}
