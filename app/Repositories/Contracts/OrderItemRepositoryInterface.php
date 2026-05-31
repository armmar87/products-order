<?php

namespace App\Repositories\Contracts;

interface OrderItemRepositoryInterface
{
    public function bulkInsert(array $items): bool;
}

