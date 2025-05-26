<?php

namespace App\Listeners\Woocommerce;

use App\Events\Woocommerce\OrderSyncEvent;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class HandleOrderSync
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(OrderSyncEvent $event): void
    {
        try {
            $wcOrder = (object)$event->order;

            if ($wcOrder->id !== null) {
                $order = Order::updateOrCreate(
                    ['order_id' => $wcOrder->id],
                    [
                        'order_status' => $wcOrder->status,
                        'total' => $wcOrder->total,
                        'total_tax' => $wcOrder->total_tax,
                        'shipping_total' => $wcOrder->shipping_total,
                        'payment_method' => $wcOrder->payment_method,
                        'created_at' => $wcOrder->date_created,
                        'updated_at' => $wcOrder->date_modified,
                    ]
                );

                // 同步订单项
                if (isset($wcOrder->line_items) && is_array($wcOrder->line_items)) {
                    foreach ($wcOrder->line_items as $item) {
                        OrderItem::updateOrCreate(
                            [
                                'order_id' => $order->id,
                                'wc_product_id' => $item->product_id,
                            ],
                            [
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                                'total' => $item->total,
                                'variation' => isset($item->variation_id) ? ['variation_id' => $item->variation_id] : null,
                            ]
                        );
                    }
                }

                Log::info('订单同步成功', [
                    'order_id' => $order->id,
                    'wc_order_id' => $wcOrder->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('订单同步失败', [
                'order_id' => $wcOrder->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
