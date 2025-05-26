<?php

namespace App\Services\Woocommerce;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Jobs\Woocommerce\SyncBatchJob;
use Automattic\WooCommerce\Client;
use App\Models\Platform;

class SyncService
{
    protected $woocommerce;
    protected $batchSize = 100; // 每批处理的数量
    public $batchId;
    public $successCount = 0;
    public $variableCount = 0;
    public $simpleCount = 0;
    public $variationCount = 0;
    public $errorCount = 0;
    public $totalCount = 0;
    public $timeStart;
    public $timeEnd;
    public $timeCost;

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        if (self::$instance !== null) {
            return;
        }
        self::$instance = $this;

        $platform = Platform::where('name', 'woocommerce')->first();
        $this->batchId = $platform->account;
        if (!$platform) {
            throw new \Exception('平台不存在');
        }

        try {
            $this->woocommerce = new Client(
                $platform->url,
                $platform->consumer_key,
                $platform->consumer_secret,
                [
                    'timeout' => 60,
                    'ssl_verify' => false,
                ]
            );
        } catch (\Exception $e) {
            Log::error('WooCommerce连接失败', [
                'url' => $platform->url,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
        $this->timeStart = microtime(true);
        $this->timeEnd = null;
        $this->timeCost = null;
        $this->successCount = 0;
        $this->errorCount = 0;
        $this->variableCount = 0;
        $this->totalCount = 0;
        $this->simpleCount = 0;
        $this->variationCount = 0;
    }

    /**
     * 获取 WooCommerce 客户端
     */
    public function getWooCommerceClient()
    {
        return $this->woocommerce;
    }

    /**
     * 批量同步产品
     */
    public function syncProducts()
    {
        $page = 1;
        $batchId = $this->batchId . '_product_sync_';
        $jobs = [];
        $this->successCount = 0;
        $this->errorCount = 0;
        $this->totalCount = 0;
        $this->variableCount = 0;
        $this->simpleCount = 0;
        $this->variationCount = 0;

        Log::info('开始同步产品');

        try {
            do {
                $products = $this->woocommerce->get('products', [
                    'per_page' => $this->batchSize,
                    'page' => $page
                ]);

                if (!empty($products)) {
                    $products = is_object($products) ? [$products] : (array)$products;
                    $jobs[] = new SyncBatchJob('product', $products, $batchId);
                }
                $page++;
            } while (!empty($products));

            if (!empty($jobs)) {
                $startTime = $this->timeStart;
                Bus::batch($jobs)
                    ->name('产品同步-' . $batchId)
                    ->allowFailures()
                    ->onConnection('redis')
                    ->onQueue('sync')
                    ->then(function () use ($batchId, $startTime,) {
                        $endTime = microtime(true);
                        $timeCost = $endTime - $startTime;

                        Log::info('产品同步完成', [
                            'batch_id' => $batchId,
                            'time_cost' => round($timeCost, 2) . '秒'
                        ]);
                    })
                    ->dispatch();

                Log::info('产品同步任务已启动', [
                    'batch_id' => $batchId
                ]);
            }

            return $batchId;
        } catch (\Exception $e) {
            Log::error('同步产品失败', [
                'batch_id' => $batchId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * 批量同步订单
     */
    public function syncOrders()
    {
        $page = 1;
        $batchId = $this->batchId . '_order_sync_';
        $jobs = [];

        do {
            $orders = $this->woocommerce->get('orders', [
                'per_page' => $this->batchSize,
                'page' => $page
            ]);

            if (!empty($orders)) {
                $orders = is_object($orders) ? [$orders] : (array)$orders;
                $jobs[] = new SyncBatchJob('order', $orders, $batchId);
            }

            $page++;
        } while (!empty($orders));

        if (!empty($jobs)) {
            Bus::batch($jobs)
                ->name('订单同步-' . $batchId)
                ->allowFailures()
                ->onConnection('redis')
                ->onQueue('sync')
                ->dispatch();
        }

        return $batchId;
    }
    /**
     * 批量同步分类
     */
    public function syncCategories()
    {
        $page = 1;
        $batchId = $this->batchId . '_category_sync_';
        $jobs = [];

        do {
            $categories = $this->woocommerce->get('products/categories', [
                'per_page' => $this->batchSize ?? 10,
                'page' => $page
            ]);

            if (!empty($categories)) {
                $categories = is_object($categories) ? [$categories] : (array)$categories;
                $jobs[] = new SyncBatchJob('category', $categories, $batchId);
            }
            $page++;
        } while (!empty($categories));

        if (!empty($jobs)) {
            Bus::batch($jobs)
                ->name('分类同步-' . $batchId)
                ->allowFailures()
                ->onConnection('redis')
                ->onQueue('sync')
                ->dispatch();
        }
        return $batchId;
    }

    /**
     * 获取同步状态
     */
    public function getSyncStatus($batchId)
    {
        $batch = Bus::findBatch($batchId);
        if (!$batch) {
            return null;
        }

        return [
            'id' => $batch->id,
            'name' => $batch->name,
            'total_jobs' => $batch->totalJobs,
            'pending_jobs' => $batch->pendingJobs,
            'failed_jobs' => $batch->failedJobs,
            'progress_percentage' => $batch->progress(),
            'finished' => $batch->finished(),
            'cancelled' => $batch->cancelled(),
        ];
    }
}
