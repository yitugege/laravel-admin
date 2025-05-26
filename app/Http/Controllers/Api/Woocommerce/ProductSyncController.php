<?php

namespace App\Http\Controllers\Api\Woocommerce;

use App\Http\Controllers\Controller;
use App\Services\Woocommerce\SyncService;
use Illuminate\Support\Facades\Log;

class ProductSyncController extends Controller
{
    protected $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function ProductSyncAll()
    {
        try {
            $batchId = $this->syncService->syncProducts();

            return response()->json([
                'status' => 'success',
                'message' => '产品同步任务已启动',
                'batch_id' => $batchId
            ]);
        } catch (\Exception $e) {
            Log::error('产品同步任务启动失败', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => '产品同步任务启动失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ProductSyncSingle($id)
    {
        try {
            $product = $this->syncService->getWooCommerceClient()->get('products/' . $id);
            event(new \App\Events\Woocommerce\ProductSyncEvent($product));

            return response()->json([
                'status' => 'success',
                'message' => '单个产品同步任务已启动'
            ]);
        } catch (\Exception $e) {
            Log::error('单个产品同步失败', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => '单个产品同步失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ProductSyncStatus($batchId)
    {
        try {
            $status = $this->syncService->getSyncStatus($batchId);

            if (!$status) {
                return response()->json([
                    'status' => 'error',
                    'message' => '未找到同步任务'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $status
            ]);
        } catch (\Exception $e) {
            Log::error('获取同步状态失败', [
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => '获取同步状态失败: ' . $e->getMessage()
            ], 500);
        }
    }
}
