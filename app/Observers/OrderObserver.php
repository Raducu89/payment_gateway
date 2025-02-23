<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Events\OrderCreated;

class OrderObserver
{
    /**
     * Executed after an order is created.
     */
    public function created(Order $order)
    {
        Log::info("Order created: {$order->id}");

        event(new OrderCreated($order));
    }

    /**
     * Excuted after an order is updated.
     */
    public function updated(Order $order)
    {
        Log::info("Order updated: {$order->id} - New status: {$order->status}");
    }
}
