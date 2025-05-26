<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Api\Woocommerce\ProductSyncController;
use App\Http\Controllers\Api\Woocommerce\OrderSyncController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\Woocommerce\CategorySyncController;


// 产品同步路由
Route::prefix('woocommerce/products')
    ->middleware(['auth:sanctum', 'permission:can_sync_products'])
    ->group(function () {
        Route::get('sync', [ProductSyncController::class, 'ProductSyncAll']);
        Route::get('sync/{id}', [ProductSyncController::class, 'ProductSyncSingle']);
        Route::get('sync/status/{batchId}', [ProductSyncController::class, 'ProductSyncStatus']);
    });

// 分类同步路由
Route::prefix('woocommerce/categories')
    ->middleware(['auth:sanctum', 'permission:can_sync_products'])
    ->group(function () {
        Route::get('sync', [CategorySyncController::class, 'CategorySyncAll']);
        Route::get('sync/{id}', [CategorySyncController::class, 'CategorySyncSingle']);
        Route::get('sync/status/{batchId}', [CategorySyncController::class, 'CategorySyncStatus']);
    });


// 订单同步路由
Route::prefix('woocommerce/orders')
    ->middleware(['auth:sanctum', 'permission:can_sync_orders'])
    ->group(function () {
        Route::get('sync', [OrderSyncController::class, 'syncAll']);
        Route::get('sync/{id}', [OrderSyncController::class, 'syncSingle']);
        Route::get('sync/status/{batchId}', [OrderSyncController::class, 'getStatus']);
    });
Route::prefix('erp')
    //->middleware(['auth:sanctum', 'permission:can_sync_products'])
    ->group(function () {
        Route::get('products', [ProductController::class, 'index']);
        Route::post('products', [ProductController::class, 'store']);
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::put('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);
    });

// 需要认证的路由
Route::middleware('auth:sanctum')->group(function () {
    // 用户相关路由
    Route::get('/user', [UserController::class, 'index']);
    Route::post('/user/logout', [UserController::class, 'logout']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::put('/user/{user}', [UserController::class, 'update']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);

    // 权限相关路由
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::post('/permissions', [PermissionController::class, 'store']);
    Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
    Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);

    // 角色相关路由
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{role}', [RoleController::class, 'show']);
    Route::put('/roles/{role}', [RoleController::class, 'update']);
    Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
    Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermissions']);
});

//不需要认证创建登录获取token
Route::post('/user/login', [UserController::class, 'login'])->name('api.login');
Route::post('/user/register', [UserController::class, 'store'])->name('api.register');
Route::get('products', [ProductController::class, 'index']);
