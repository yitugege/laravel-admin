<?php

namespace App\Http\Controllers\Api\Woocommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Woocommerce\SyncService;
use Illuminate\Support\Facades\Log;

class OrderSyncController extends Controller
{
    protected $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function syncAll()
    {
        try {
            $batchId = $this->syncService->syncOrders();

            return response()->json([
                'status' => 'success',
                'message' => '订单同步任务已启动',
                'batch_id' => $batchId
            ]);
        } catch (\Exception $e) {
            Log::error('订单同步任务启动失败', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => '订单同步任务启动失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncSingle($id)
    {
        try {
            $order = $this->syncService->getWooCommerceClient()->get('orders/' . $id);
            event(new \App\Events\Woocommerce\OrderSyncEvent($order));

            return response()->json([
                'status' => 'success',
                'message' => '单个订单同步任务已启动'
            ]);
        } catch (\Exception $e) {
            Log::error('单个订单同步失败', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => '单个订单同步失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStatus($batchId)
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
