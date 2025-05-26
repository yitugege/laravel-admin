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
            $table->unsignedBigInteger('order_id')->unique()->comment('订单ID');

            $table->unsignedBigInteger('customer_id')->nullable()->comment('用户id');
            $table->string('order_status')->comment('订单状态');
            $table->decimal('total', 10, 2)->default(0)->comment('订单金额');
            $table->decimal('total_tax', 10, 2)->default(0)->comment('税费总额');
            $table->decimal('shipping_total', 10, 2)->default(0)->comment('运费');
            $table->string('payment_method')->comment('支付方式')->nullable();
           // $table->timestamp('created_at')->comment('创建时间')->nullable();
            //$table->timestamp('updated_at')->comment('更新时间')->nullable();
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
