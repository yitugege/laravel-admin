<?php

namespace App\Listeners\Woocommerce;

use App\Events\Woocommerce\CategorySyncEvent;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleCategorySync implements ShouldQueue
{
    use InteractsWithQueue;

    public $connection = 'redis';
    public $queue = 'sync';
    public $tries = 3;
    public $timeout = 300;

    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(CategorySyncEvent $event): void
    {
        try {
            $wcCategory = (object)$event->category;

            if (empty($wcCategory->id)) {
                Log::warning('分类ID为空,跳过同步', [
                    'name' => $wcCategory->name ?? 'unknown'
                ]);
                return;
            }

            // 基础分类数据
            $categoryData = [
                'id' => $wcCategory->id,
                'name' => $wcCategory->name,
                'slug' => $wcCategory->slug,
                'description' => $wcCategory->description ?? '',
                'parent' => $wcCategory->parent ?? null,
                'count' => $wcCategory->count ?? 0,
            ];

            // 处理图片数据
            if ($wcCategory->image) {
                $categoryData['image'] = json_encode([
                    'src' => $wcCategory->image->src ?? null,
                    // 'name' => $wcCategory->image->name,
                    // 'alt' => $wcCategory->image->alt,
                    // 'date_created' => $wcCategory->image->date_created,
                    // 'date_modified' => $wcCategory->image->date_modified
                ]);
            }

            // 更新或创建分类
            Category::updateOrCreate(
                ['id' => $wcCategory->id],
                $categoryData
            );

            Log::info('分类同步成功', [
                'category_id' => $wcCategory->id,
                'name' => $wcCategory->name
            ]);
        } catch (\Exception $e) {
            Log::error('分类同步失败', [
                'category_id' => $wcCategory->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
