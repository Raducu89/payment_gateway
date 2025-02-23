<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PayTransactionRequest;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use App\Services\Payment\PayPalPaymentProvider; 
use App\Services\Payment\StripePaymentProvider;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\PaymentStatus;
use Faker\Provider\ar_EG\Payment;

class TransactionController extends Controller
{
    protected PaymentService $paymentService;
    protected OrderRepository $orderRepository;
    protected TransactionRepository $transactionRepository;

    public function __construct(
        OrderRepository $orderRepository,
        TransactionRepository $transactionRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Process payment for an order using the bound provider.
     *
     * @param  PayTransactionRequest  $request
     * @param  int  $orderId
     * @return JsonResponse
     */
    public function pay(Request $request, $orderId): JsonResponse //temporary removed PayTransactionRequest $request
    {
        // Retrieve the order from the repository.
        $order = $this->orderRepository->findById($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if($order->status !== PaymentStatus::Pending->value) {
            return response()->json(['message' => 'Order is not pending'], 400);
        }   

        // Dispatch a job to process the payment asynchronously.
        \App\Jobs\ProcessPaymentJob::dispatch($order);

        return response()->json([
            'message' => 'Payment processing initiated.',
            'data'    => [
                'order_id' => $order->id,
                'status'   => 'processing',
            ],
        ]);
    }

    /**
     * Show details of a transaction.
     *
     * @param  int  $transactionId
     * @return JsonResponse
     */
    public function show($transactionId): JsonResponse
    {
        $transaction = $this->transactionRepository->findById($transactionId);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json([
            'data' => $transaction
        ]);
    }
}
