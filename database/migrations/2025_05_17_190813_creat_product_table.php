<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku')->nullable()->unique()->comment('产品sku');
            //woocommerce_id 必须
            $table->unsignedBigInteger('woocommerce_id')->unique()->comment('woocommerce的id');
            $table->enum('type', ['simple', 'variable', 'variation'])->comment('产品类型');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('父级id');
            $table->string('name')->nullable()->comment('产品名称');
            $table->enum('status', ['publish', 'draft', 'pending', 'private'])->default('publish')->comment('产品状态');
            $table->boolean('featured')->default(false)->comment('是否推荐');
            $table->LONGTEXT('description')->nullable()->comment('产品描述');
            $table->text('short_description')->nullable()->comment('产品短描述');
            $table->decimal('price', 10, 2)->nullable()->comment('原价');
            $table->decimal('regular_price', 10, 2)->nullable()->comment('现价');
            $table->decimal('sale_price', 10, 2)->nullable()->comment('销售价');
            $table->json('meta_data')->nullable()->comment('产品元数据');
            $table->integer('stock_quantity')->nullable()->comment('库存数量');
            $table->enum('stock_status', ['instock', 'outofstock', 'onbackorder'])->default('instock')->comment('库存状态');
            $table->unsignedBigInteger('category_id')->nullable()->comment('产品分类');
            $table->json('tags')->nullable()->comment('产品标签');
            $table->json('images')->nullable()->comment('产品图片');
            $table->json('attributes')->nullable()->comment('产品属性');
            $table->timestamps();


            // 索引
            $table->index('woocommerce_id');
            $table->index('parent_id');
            $table->index('type');
            $table->index('sku');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
