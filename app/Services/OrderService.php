<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use App\Enums\PaymentStatus;

class OrderService
{   
    protected OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Create a new order.
     *
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function createOrder(array $data): Order
    {
        // Wrap order creation in a DB transaction for data integrity.
        return DB::transaction(function () use ($data) {
            $order = $this->orderRepository->create([
                'user_id' => Auth::id(),
                'amount'  => $data['amount'],
                'status'  => PaymentStatus::Pending->value,
            ]);

            $order->transaction()->create([
                'payment_provider' => $data['payment_provider'],
                'status' => PaymentStatus::Pending,
            ]);

            return $order;
        });
    }

    /**
     * Retrieve an order by its ID.
     *
     * @param int $orderId
     * @return Order|null
     */
    public function getOrder(int $orderId): ?Order
    {
        return $this->orderRepository->findById($orderId);
    }


    /**
     * Retrieve last 10 orders.
     *
     * @return Order[]
     */
    public function getOrders(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->orderRepository->listOrders($userId);
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        return $this->orderRepository->updateStatus($order, $status);
    }
}
