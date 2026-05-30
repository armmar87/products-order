<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Symfony\Component\HttpFoundation\Response;

class OrderController
{
    use AuthorizesRequests;
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    public function store(StoreOrderRequest $request)
    {
        $this->authorize('create', Order::class);

        $orderId = $this->orderService->order($request->validated());

        return response()->json(['order_id' => $orderId], Response::HTTP_CREATED);
    }
}
