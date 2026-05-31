<?php

namespace App\Repositories;

use App\Filters\ProductFilter;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    protected Product $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    protected function baseQuery(): Builder
    {
        return $this->model->newQuery()->with([
            'category:id,name,slug',
            'brand:id,name,slug',
            'primaryImage:id,product_id,image_path,alt_text',
            'inventory:id,product_id,sku,stock,reserved_stock'
        ]);
    }

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        $filter = new ProductFilter($query, $filters);
        $query = $filter->apply();

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->search($filters, $perPage);
    }

    public function findByIdsWithLock(array $ids): Collection
    {
        return $this->model->newQuery()
            ->with('inventory')
            ->whereIn('id', $ids)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    public function decrementStock(int $productId, int $quantity): int
    {
        return ProductInventory::query()
            ->where('product_id', $productId)
            ->decrement('stock', $quantity);
    }
}


