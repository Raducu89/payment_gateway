<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Enums\PaymentStatus;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder([
            'user_id' => $request->user()->id,
            'amount' => $request->validated()['amount'],
            'status' => PaymentStatus::Pending,
        ]);

        $order->transaction()->create([
            'payment_provider' => $request->validated()['payment_provider'],
            'status' => PaymentStatus::Pending,
        ]);

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $order
        ], 201);
    }

    public function show($id): OrderResource|JsonResponse
    {   
        Gate::authorize('view', Order::find($id));

        $order = $this->orderService->getOrder($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return new OrderResource($order);
    }

    public function list(Request $request): JsonResponse  // ToDo: implement OrderResource,pagination
    {
        $orders = $this->orderService->getOrders($request->user()->id);
        
        return response()->json([
            'data' => $orders
        ]);
    }
}
