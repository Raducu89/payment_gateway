<?php

namespace App\Services\Payment;

use App\Models\Order;

interface PaymentProviderInterface
{   
    /**
     * Process payment for an order.
     *
     * @param Order $order
     * @param array $paymentDetails
     * @return array
     */
    public function processPayment(Order $order, array $paymentDetails): array;

    /**
     * Refund a payment for an order.
     *
     * @param Order $order
     * @return array
     */
    public function refundPayment(Order $order): array;

    /**
     * Verify payment status for an order.
     *
     * @param int $transactionId
     * @return array
     */
    public function verifyPayment(int $transactionId): mixed;

    /**
     * Handle a webhook payload from the payment provider.
     *
     * @param array $payload
     * @return array
     */
    public function handleWebhook(array $payload): array;
}
