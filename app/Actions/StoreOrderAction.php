<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Exceptions\ProductItemOutOfStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class StoreOrderAction
{

    public function execute(array $items): int
    {
        $user = Auth::user();

        $quantities = collect($items)->pluck('quantity', 'product_id');
        $productIds = $quantities->keys()->all();

        return DB::transaction(function () use ($user, $quantities, $productIds) {

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

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

            $orderId = Order::query()->insertGetId([
                'user_id'    => $user->id,
                'total'      => $total,
                'status'     => OrderStatus::Pending->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($orderItemsData as &$item) {
                $item['order_id'] = $orderId;

                Product::query()
                    ->where('id', $item['product_id'])
                    ->decrement('stock', $item['quantity']);
            }

            OrderItem::query()->insert($orderItemsData);

            return $orderId;
        });
    }

}
