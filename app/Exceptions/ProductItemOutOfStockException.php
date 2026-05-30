<?php

namespace App\Exceptions;

use RuntimeException;

final class ProductItemOutOfStockException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("Product not found.");
    }
}
