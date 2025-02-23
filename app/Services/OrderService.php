<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\PaymentStatus;
use Faker\Provider\ar_EG\Payment;

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
            return $this->orderRepository->create([
                'user_id' => Auth::id(),
                'amount'  => $data['amount'],
                'status'  => PaymentStatus::Pending->value,
            ]);
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
    public function getOrders(int $userId): array
    {
        return $this->orderRepository->listOrders($userId);
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        return $this->orderRepository->updateStatus($order, $status);
    }
}
