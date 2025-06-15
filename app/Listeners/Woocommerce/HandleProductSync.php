<?php

namespace App\Listeners\Woocommerce;

use App\Events\Woocommerce\ProductSyncEvent;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Bus;
use App\Jobs\Woocommerce\SyncBatchJob;
use App\Services\Woocommerce\SyncService;

class HandleProductSync implements ShouldQueue
{
    use InteractsWithQueue;

    public $connection = 'redis';
    public $queue = 'sync';
    public $tries = 3;
    public $timeout = 300;
    protected $syncService;
    protected $batchId;

    /**
     * Create the event listener.
     */
    public function __construct(SyncService $syncService)
    {
        $this->syncService = SyncService::getInstance();
        $this->batchId = $this->syncService->batchId;
    }


    /**
     * Handle the event.
     */
    public function handle(ProductSyncEvent $event): void
    {
        try {
            $wcProduct = (object)$event->product; // 将数组转换回对象
            $this->syncService->totalCount++; // 在处理产品之前就增加总计数

            if (empty($wcProduct->id)) {
                $this->syncService->errorCount++;
                Log::warning('产品ID为空,跳过同步', [
                    'sku' => $wcProduct->sku ?? 'unknown'
                ]);
                return;
            }

            // 基础产品数据
            $productData = [
                'parent_id' => $wcProduct->parent_id ?? null,
                'type' => $wcProduct->type,
                'name' => $wcProduct->name,
                'status' => $wcProduct->status,
                'featured' => $wcProduct->featured ?? false,
                'description' => $wcProduct->description ?? '',
                'short_description' => $wcProduct->short_description ?? '',
                'sku' => $wcProduct->sku,
                'price' => $this->parsePrice($wcProduct->price) ?? 0,
                'regular_price' => $this->parsePrice($wcProduct->regular_price) ?? 0,
                'sale_price' => $this->parsePrice($wcProduct->sale_price) ?? 0,
                'stock_quantity' => $wcProduct->stock_quantity,
                'stock_status' => $wcProduct->stock_status,
                'tags' => collect($wcProduct->tags ?? [])->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name
                ])->all(),
                'images' => collect($wcProduct->images ?? [])->map(fn($image) => [

                    'src' => $image->src,

                ])->all(), // all() 是 toArray() 的别名

                'meta_data' => collect($wcProduct->meta_data ?? [])->filter(function ($meta) {
                    return $meta->key === '_fixed_price_rules';
                })->map(fn($meta) => [
                    'value' => $meta->value
                ])->all()
            ];

            if ($wcProduct->type === 'variable') {

                $this->syncService->variableCount++;
                $productData['category_id'] = $wcProduct->categories[0]->id ?? null;
                $productData['attributes'] = collect($wcProduct->attributes ?? [])->map(fn($attribute) => [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'slug' => $attribute->slug,
                    'option' => $attribute->options
                ])->all();
            } elseif ($wcProduct->type === 'variation') {
                $this->syncService->variationCount++;
                $parentProduct = $this->syncService->getWooCommerceClient()->get('products/' . $wcProduct->parent_id);
                $productData['type'] = 'variation';
                $productData['parent_id'] = $wcProduct->parent_id;
                $productData['description'] = $parentProduct->description;
                $productData['short_description'] = $parentProduct->short_description;
                $productData['category_id'] = $parentProduct->categories[0]->id ?? null;
                $productData['tags'] = collect($parentProduct->tags ?? [])->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name
                ])->all();
                $productData['attributes'] = collect($wcProduct->attributes ?? [])->map(fn($attribute) => [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'slug' => $attribute->slug,
                    'option' => $attribute->option
                ])->all();
            } else {
                $this->syncService->simpleCount++;
                //不需要该参数
                $productData['category_id'] = $wcProduct->categories[0]->id ?? null;
                $productData['attributes'] = null;
            }

            // 同步主产品
            try {
                $product = Product::updateOrCreate(
                    ['woocommerce_id' => $wcProduct->id],
                    $productData
                );
            } catch (\Exception $e) {
                Log::error('产品同步失败', [
                    'product_id' => $wcProduct->id,
                    'sku' => $wcProduct->sku,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            if ($product->wasRecentlyCreated) {
                $this->syncService->successCount++;
            } else {
                $this->syncService->errorCount++;
            }

            // 同步变体产品
            if ($wcProduct->type === 'variable' && !empty($wcProduct->variations)) {
                $batchId = $this->syncService->batchId . '_product_sync_';
                $jobs = [];

                foreach ($wcProduct->variations as $variationId) {
                    if (empty($variationId)) {
                        $this->syncService->errorCount++;
                        Log::warning('变体产品ID为空,跳过同步', [
                            'parent_id' => $wcProduct->id,
                            'sku' => $wcProduct->sku ?? 'unknown'
                        ]);
                        continue;
                    }
                    $child_product = array($this->syncService->getWooCommerceClient()->get('products/' . $variationId));
                    // 创建变体同步任务
                    $jobs[] = new SyncBatchJob('product', $child_product, $batchId);
                }

                if (!empty($jobs)) {
                    Bus::batch($jobs)
                        ->name('变体产品同步-' . $batchId)
                        ->allowFailures()
                        ->onConnection('redis')
                        ->onQueue('sync')
                        ->dispatch();
                }
            }

            // 输出统计信息
            Log::info('产品同步统计第' . $this->syncService->totalCount . '个', [
                'product_id' => $wcProduct->id,
                'sku' => $product->sku,
                'type' => $product->type,
                'success_count' => $this->syncService->successCount,
                'error_count' => $this->syncService->errorCount,
                'total_count' => $this->syncService->totalCount,
                'variable_count' => $this->syncService->variableCount,
                'simple_count' => $this->syncService->simpleCount,
                'variation_count' => $this->syncService->variationCount
            ]);
        } catch (\Exception $e) {
            Log::error('产品同步失败统计', [
                'product_id' => $wcProduct->id ?? 'unknown',
                'sku' => $wcProduct->sku ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function parsePrice($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }
        return is_numeric($value) ? (float)$value : 0;
    }
}
