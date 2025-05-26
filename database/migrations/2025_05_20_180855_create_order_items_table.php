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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('wc_product_id')->comment('woocommerce商品ID');
            $table->integer('quantity')->comment('数量')->default(1);
            $table->decimal('price', 10, 2)->comment('价格')->default(0);
            $table->decimal('total', 10, 2)->comment('总价')->default(0);
            $table->json('variation')->nullable(); // 变体信息
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
