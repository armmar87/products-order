<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Exceptions\ProductItemOutOfStockException;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class StoreOrderAction
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderItemRepositoryInterface $orderItemRepository
    ) {
    }

    public function execute(array $items): int
    {
        $user = Auth::user();

        $quantities = collect($items)->pluck('quantity', 'product_id');
        $productIds = $quantities->keys()->all();

        return DB::transaction(function () use ($user, $quantities, $productIds) {

            $products = $this->productRepository->findByIdsWithLock($productIds);

            $total = 0;
            $orderItemsData = [];

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);

                if (!$product || $product->stock < $quantity) {
                    throw new ProductItemOutOfStockException();
                }

                $total += $product->price * $quantity;

                $orderItemsData[] = [
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'price'      => $product->price,
                ];
            }

            $orderId = $this->orderRepository->create([
                'user_id'    => $user->id,
                'total'      => $total,
                'status'     => OrderStatus::Pending->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($orderItemsData as &$item) {
                $item['order_id'] = $orderId;

                $this->productRepository->decrementStock($item['product_id'], $item['quantity']);
            }

            $this->orderItemRepository->bulkInsert($orderItemsData);

            return $orderId;
        });
    }

}
