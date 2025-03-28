<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated());

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $order
        ], 201);
    }

    public function show($id): OrderResource|JsonResponse
    {   
        
        $order = $this->orderService->getOrder($id);
        
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        Gate::authorize('view', $order);

        return new OrderResource($order);
    }

    public function list(Request $request): JsonResponse 
    {
        $orders = $this->orderService->getOrders($request->user()->id);
        
        return response()->json([
            'data' => OrderResource::collection($orders)
        ]);
    }
}
