<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Support\Facades\Log;

class SendOrderCreatedNotification
{
    /**
     * Handle event.
     */
    public function handle(OrderCreated $event)
    {
        Log::info("Notification: Order created with id {$event->order->id}");
    }
}
