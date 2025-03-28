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
use Illuminate\Support\Facades\DB;
use App\Enums\PaymentStatus;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Order $order;

    // Set the number of times the job should be attempted
    public int $tries = 3;

    // Set the number of seconds the job should wait before retrying
    public array $backoff = [10, 30, 60];

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
    public function handle(OrderRepository $orderRepo, TransactionRepository $transactionRepo): void
    {      
        try {
            DB::beginTransaction();

            // Retrieve the provider key from the order's transaction data
            $transaction = $this->order->transaction;
            $providerKey = $transaction->payment_provider;
    
            // Use the factory to create the appropriate provider instance
            $paymentProvider = PaymentProviderFactory::make($providerKey);
    
            // Create a new PaymentService instance and process the payment
            $paymentService = new PaymentService($paymentProvider, $orderRepo, $transactionRepo);

            $paymentService->processPayment($this->order);

            DB::commit();

            Log::info('Payment processed successfully', [
                'order_id' => $this->order->id,
                'transaction_id' => $transaction->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($transaction)) {
                $transactionRepo->updateStatus($transaction, PaymentStatus::Failed->value);
            }

            $this->order->update(['status' => PaymentStatus::Failed->value]);

            Log::error('Payment processing failed', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw $e;
        }
    }

    /**
     * The job failed to process.
     *
     * @param \Throwable $e
     * @return void
     */
    public function failed(\Throwable $e)
    {
        Log::error('ProcessPaymentJob failed hard', [
            'order_id' => $this->order->id,
            'error' => $e->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}
