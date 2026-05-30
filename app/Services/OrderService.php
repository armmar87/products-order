<?php

namespace App\Services;

use App\Actions\StoreOrderAction;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Support\Facades\Auth;

final class OrderService
{
    public function __construct(
        private readonly StoreOrderAction $storeOrderAction,
        private readonly SlackService $slackService,
    ) {}

    public function order(array $data): int
    {
        $user = Auth::user();

        $orderId = $this->storeOrderAction->execute($data);

        $user->notify(new OrderCreatedNotification($orderId));

        $this->slackService->send($orderId);

        return $orderId;
    }
}
