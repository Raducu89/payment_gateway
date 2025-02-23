<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use App\Services\Payment\PaymentProviderInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Enums\PaymentStatus;

class PaymentService
{
    protected PaymentProviderInterface $paymentProvider;
    protected OrderRepository $orderRepo;
    protected TransactionRepository $transactionRepo;

    public function __construct(
        PaymentProviderInterface $paymentProvider,
        OrderRepository $orderRepo,
        TransactionRepository $transactionRepo
    ) {
        $this->paymentProvider = $paymentProvider;
        $this->orderRepo = $orderRepo;
        $this->transactionRepo = $transactionRepo;
    }

    public function processPayment(Order $order): array
    {
        $transaction = $order->transaction;

        // Prevent duplicate payment processing
        if ($transaction->status !== PaymentStatus::Pending->value) {
            throw new \Exception("Payment already processed for this order");
        }

        
        try {
            $paymentResult = $this->paymentProvider->pay($order);
        } catch (Exception $e) {
            Log::error("Payment processing error for order {$order->id}: " . $e->getMessage());

            $this->transactionRepo->updateStatus($transaction, PaymentStatus::Failed->value, ['error' => $e->getMessage()]);
            $this->orderRepo->updateStatus($order, PaymentStatus::Failed->value);

            throw $e;
        }

        $status = $paymentResult['status'] === 'success' ? PaymentStatus::Paid->value : PaymentStatus::Failed->value;

        $this->transactionRepo->updateStatus($transaction, $status, $paymentResult['response'] ?? []);

        $this->orderRepo->updateStatus($order, $status);

        return [
            'transaction_id' => $transaction->id,
            'transaction_status' => $status,
            'response' => $paymentResult['response'] ?? [],
        ];
    }
}
