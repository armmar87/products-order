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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('brand_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->string('price');
            $table->string('compare_price');
            $table->integer('stock');
            $table->integer('min_stock');
            $table->string('sku');
            $table->string('barcode');
            $table->string('weight');
            $table->string('status');
            $table->boolean('is_featured');
            $table->integer('views');
            $table->integer('sales_count');
            $table->json('images');
            $table->json('attributes');
            $table->json('tags');
            $table->string('meta_title');
            $table->text('meta_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
