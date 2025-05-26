<?php

namespace App\Events\Woocommerce;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CategorySyncEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels, InteractsWithQueue;

    public $category;
    public $type;
    public $tries = 3; // 最大重试次数
    public $timeout = 300; // 超时时间（秒）
    public $maxExceptions = 3; // 最大异常次数
    public $connection = 'redis'; // 指定连接
    public $queue = 'sync'; // 指定队列

    /**
     * Create a new event instance.
     *
     * @param mixed $category
     * @param string $type
     */
    public function __construct($category, string $type = 'sync')
    {
        $this->category = (array)$category; // 将对象转换为数组
        $this->type = $type;
    }

    /**
     * 获取队列优先级
     */
    public function priority()
    {
        return 'low'; // 低优先级
    }
}
