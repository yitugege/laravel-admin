<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'order_platform',
        'order_status',
        'currency',
        'total',
        'total_tax',
        'shipping_total',
        'payment_method',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function OrderStatusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }
    public function Customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
