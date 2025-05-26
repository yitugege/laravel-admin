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
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->commit('分类名称');
            $table->text('description')->nullable()->commit('分类描述');
            $table->string('image')->nullable()->commit('分类图片');
            $table->boolean('status')->default(true)->commit('分类状态'); //是否启用
            $table->unsignedBigInteger('parent')->nullable()->commit('父级id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
