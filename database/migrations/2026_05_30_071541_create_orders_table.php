<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('coupon_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('status');
            $table->string('total');
            $table->string('subtotal');
            $table->string('tax');
            $table->string('discount');
            $table->string('shipping_cost');
            $table->string('currency');
            $table->text('shipping_address');
            $table->text('billing_address');
            $table->string('payment_method');
            $table->string('payment_status');
            $table->foreignId('transaction_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('notes');
            $table->string('ip_address');
            $table->timestamp('paid_at');
            $table->timestamp('shipped_at');
            $table->timestamp('delivered_at');
            $table->timestamp('cancelled_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
