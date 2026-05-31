<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function search(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
}

