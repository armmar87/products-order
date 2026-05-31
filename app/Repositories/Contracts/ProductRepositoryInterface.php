<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function search(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function findByIdsWithLock(array $ids): Collection;

    public function decrementStock(int $productId, int $quantity): int;
}

