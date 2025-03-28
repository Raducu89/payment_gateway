<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Listeners\SendOrderCreatedNotification;
use Illuminate\Support\ServiceProvider;
use App\Services\Payment\PaymentProviderInterface;
use Illuminate\Support\Facades\Event;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {        
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        Event::listen(OrderCreated::class, SendOrderCreatedNotification::class);
    }
}
