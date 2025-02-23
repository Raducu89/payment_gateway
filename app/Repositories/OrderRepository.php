<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function findById(int $id): ?Order
    {
        return Order::find($id);
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        return $order;
    }

    public function listOrders(): array
    {
        return Order::orderBy('created_at', 'desc')->with('transaction')->limit(10)->get()->toArray();
    }
    
}
