<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function search(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 15), 100);

        $filters = ['search' => $request->input('q'),];

        $filters = array_filter($filters, fn($value) => $value !== null);

        $products = $this->productRepository->search($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
        ]);
    }
}

