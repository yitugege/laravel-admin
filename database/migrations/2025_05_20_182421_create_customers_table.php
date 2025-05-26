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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->comment('客户ID');
            $table->string('email')->comment('邮箱');
            $table->string('first_name')->comment('名字');
            $table->string('last_name')->comment('姓氏');
            $table->string('phone')->comment('电话');
            $table->string('address')->comment('地址');
            $table->decimal('total_spent', 10, 2)->comment('总消费')->default(0);
            $table->integer('order_count')->comment('订单数量')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
