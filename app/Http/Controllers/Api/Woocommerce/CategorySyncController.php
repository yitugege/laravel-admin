<?php

namespace App\Http\Controllers\Api\Woocommerce;

use App\Http\Controllers\Controller;
use App\Services\Woocommerce\SyncService;
use Illuminate\Support\Facades\Log;

class CategorySyncController extends Controller
{
    protected $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function CategorySyncAll()
    {
        try {
            $batchId = $this->syncService->syncCategories();

            return response()->json([
                'status' => 'success',
                'message' => '分类同步任务已启动',
                'batch_id' => $batchId
            ]);
        } catch (\Exception $e) {
            Log::error('分类同步任务启动失败', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => '分类同步任务启动失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function CategorySyncSingle($id)
    {
        try {
            $category = $this->syncService->getWooCommerceClient()->get('products/categories/' . $id);
            event(new \App\Events\Woocommerce\CategorySyncEvent($category));

            return response()->json([
                'status' => 'success',
                'message' => '单个分类同步任务已启动'
            ]);
        } catch (\Exception $e) {
            Log::error('单个分类同步失败', [
                'category_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => '单个分类同步失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function CategorySyncStatus($batchId)
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
