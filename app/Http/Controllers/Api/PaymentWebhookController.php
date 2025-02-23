<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\TransactionRepository;
use App\Repositories\OrderRepository;
use App\Http\Requests\PaymentWebhookRequest;
use App\Enums\PaymentStatus;
use Faker\Provider\ar_EG\Payment;

class PaymentWebhookController extends Controller
{
    protected TransactionRepository $transactionRepo;
    protected OrderRepository $orderRepo;

    public function __construct(
        TransactionRepository $transactionRepo,
        OrderRepository $orderRepo
    ) {
        $this->transactionRepo = $transactionRepo;
        $this->orderRepo = $orderRepo;
    }

    /**
     * Handle incoming webhook notifications from payment providers.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(PaymentWebhookRequest $request)
    {
        // Authenticate the webhook request using a shared secret token.
        $secret = $request->header('X-Webhook-Token');
        if ($secret !== config('payment.webhook_secret')[$request->payment_provider]) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validated();

        // Retrieve the transaction.
        $transaction = $this->transactionRepo->findById($data['transaction_id']);
        if (!$transaction || $transaction->status !== PaymentStatus::Pending->value || $transaction->payment_provider !== $request->payment_provider) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Update the transaction status.
        $this->transactionRepo->updateStatus($transaction, $data['status'], $data['response'] ?? []);

        // Optionally, update the associated order status.
        $order = $transaction->order;
        if ($order) {
            if ($data['status'] === PaymentStatus::Paid->value) {
                $this->orderRepo->updateStatus($order, PaymentStatus::Paid->value);
            } elseif ($data['status'] === PaymentStatus::Failed->value) {
                $this->orderRepo->updateStatus($order, PaymentStatus::Failed->value);
            }
        }

        return response()->json(['message' => 'Webhook processed successfully.']);
    }
}
