<?php

namespace App\Jobs\Woocommerce;

use App\Events\Woocommerce\CategorySyncEvent;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\Woocommerce\ProductSyncEvent;
use App\Events\Woocommerce\OrderSyncEvent;

class SyncBatchJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable;

    protected $syncType;
    protected $items;
    public $tries = 3; // 最大重试次数
    public $timeout = 300; // 超时时间（秒）

    /**
     * Create a new job instance.
     */
    public function __construct(string $syncType, array $items, string $batchId)
    {
        $this->syncType = $syncType;
        $this->items = $items;
        $this->batchId = $batchId;
        $this->onConnection('redis');
        $this->onQueue('sync');
        //异步执行同步产品,避免阻塞队列
       // $this->dispatch();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            foreach ($this->items as $item) {
                try {
                    if ($this->syncType === 'product') {
                        //添加事件监听器
                        event(new ProductSyncEvent($item));
                    } elseif ($this->syncType === 'order') {
                        event(new OrderSyncEvent($item));
                    } elseif ($this->syncType === 'category') {
                        event(new CategorySyncEvent(($item)));
                    }
                } catch (\Exception $e) {
                    Log::error("单个项目同步失败", [
                        'batch_id' => $this->batchId,
                        'sync_type' => $this->syncType,
                        'item' => $item,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("分页同步失败", [
                'batch_id' => $this->batchId,
                'sync_type' => $this->syncType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * 处理失败的任务
     */
    public function failed(\Throwable $exception)
    {
        Log::error("分页同步失败", [
            'batch_id' => $this->batchId,
            'sync_type' => $this->syncType,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
