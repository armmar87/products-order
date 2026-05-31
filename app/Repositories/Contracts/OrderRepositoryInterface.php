<?php

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface
{
    public function create(array $data): int;
}

