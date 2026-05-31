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
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'refunded', 'on_hold'])->default('pending');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_number');
            $table->index(['user_id', 'status']);
            $table->index('status');
        });

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('type', ['shipping', 'billing']);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city', 100);
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20);
            $table->string('country', 2); // ISO country code
            $table->timestamps();

            $table->index(['order_id', 'type']);
        });

        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->string('payment_method', 50);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('transaction_reference')->nullable();
            $table->text('payment_details')->nullable(); // JSON or encrypted payment details
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique('order_id');
            $table->index('payment_status');
        });

        Schema::create('order_fulfillments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('tracking_number')->nullable();
            $table->string('carrier', 100)->nullable();
            $table->string('shipping_method', 100)->nullable();
            $table->enum('fulfillment_status', ['pending', 'processing', 'shipped', 'delivered', 'returned'])->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('expected_delivery_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('order_id');
            $table->index('tracking_number');
            $table->index('fulfillment_status');
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('status', 50);
            $table->text('comment')->nullable();
            $table->boolean('notify_customer')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_fulfillments');
        Schema::dropIfExists('order_payments');
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('orders');
    }
};
