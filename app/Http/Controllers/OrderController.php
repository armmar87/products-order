<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController
{
    public function store(StoreOrderRequest $request)
    {
        $user = Auth::user();

        if ($user->role != 'customer') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        if (!$request->items || count($request->items) == 0) {
            return response()->json(['error' => 'No items'], 400);
        }

        $total = 0;
        foreach ($request->items as $item) {
            $product = DB::table('products')->where('id', $item['product_id'])->first();
            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }
            if ($product->stock < $item['quantity']) {
                return response()->json(['error' => 'Not enough stock'], 400);
            }
            $total += $product->price * $item['quantity'];
        }

        $order = DB::table('orders')->insertGetId([
            'user_id' => $user->id,
            'total'   => $total,
            'status'  => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($request->items as $item) {
            $product = DB::table('products')->where('id', $item['product_id'])->first();
            DB::table('order_items')->insert([
                'order_id'   => $order,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $product->price,
            ]);
            DB::table('products')
                ->where('id', $item['product_id'])
                ->decrement('stock', $item['quantity']);
        }

        // send email
        Mail::send('emails.order', ['order_id' => $order], function($m) use ($user) {
            $m->to($user->email)->subject('Order placed');
        });

        // notify admin via Slack
        $client = new \GuzzleHttp\Client();
        $client->post('https://hooks.slack.com/services/XXX/YYY/ZZZ', [
            'json' => ['text' => 'New order: #' . $order]
        ]);

        return response()->json(['order_id' => $order], 201);
    }
}
