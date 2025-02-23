<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\OrderService;
use App\Repositories\OrderRepository;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery;

class OrderServiceTest extends TestCase
{
    public function testCreateOrderSuccessfully()
    {
        $userId = 1;
        Auth::shouldReceive('id')->andReturn($userId);

        $orderData = ['amount' => 100.00];

        $orderRepositoryMock = Mockery::mock(OrderRepository::class);
        $orderRepositoryMock->shouldReceive('create')
            ->once()
            ->with(\Mockery::subset([
                'user_id' => $userId,
                'amount'  => $orderData['amount'],
                'status'  => 'pending',
            ]))
            ->andReturn(new Order([
                'user_id' => $userId,
                'amount'  => $orderData['amount'],
                'status'  => 'pending',
            ]));

        $orderService = new OrderService($orderRepositoryMock);
        $order = $orderService->createOrder($orderData);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals($orderData['amount'], $order->amount);
    }
}
