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
        Schema::create('platforms', function (Blueprint $table) {
            $table->id()->comment('平台ID编号');
            $table->string('name')->comment('平台名称');
            $table->string('account')->comment('平台账号');
            $table->string('url')->comment('平台URL');
            $table->text('consumer_key')->comment('平台API密钥');
            $table->text('consumer_secret')->comment('平台API密钥');
            $table->integer('timeout')->comment('平台超时时间');
            $table->boolean('ssl_verify')->comment('平台SSL验证');
            $table->string('version')->comment('API版本');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};
