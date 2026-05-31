<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\Contracts\OrderItemRepositoryInterface;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    protected OrderItem $model;

    public function __construct(OrderItem $model)
    {
        $this->model = $model;
    }

    public function bulkInsert(array $items): bool
    {
        return $this->model->newQuery()->insert($items);
    }
}

