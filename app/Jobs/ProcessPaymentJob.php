<?php

namespace App\Jobs;

use App\Models\Order;
use App\Factories\PaymentProviderFactory;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use App\Services\PaymentService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Order $order;

    // Set the number of times the job should be attempted
    public int $tries = 3;

    // Set the number of seconds the job should wait before retrying
    public int $backoff = 10;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @param OrderRepository $orderRepo
     * @param TransactionRepository $transactionRepo
     */
    public function handle(OrderRepository $orderRepo, TransactionRepository $transactionRepo)
    {
        // Retrieve the provider key from the order's transaction data
        $providerKey = $this->order->transaction->payment_provider;

        // Use the factory to create the appropriate provider instance
        $paymentProvider = PaymentProviderFactory::make($providerKey);

        // Create a new PaymentService instance and process the payment
        $paymentService = new PaymentService($paymentProvider, $orderRepo, $transactionRepo);
        
        try {
            $paymentService->processPayment($this->order);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * The job failed to process.
     *
     * @param Exception $exception
     */
    public function failed(Exception $e)
    {
        $transaction = $this->order->transaction;
        if ($transaction) {
            $transaction->update([
                'status'        => 'failed',
                'response_data' => ['error' => $e->getMessage()],
            ]);
        }

        Log::error('ProcessPaymentJob failed for order ' . $this->order->id, [
            'exception' => $e->getMessage()
        ]);
    }
}
