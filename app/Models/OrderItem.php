<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'wc_product_id',
        'quantity',
        'price',
        'total',
        'variation',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'variation' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'wc_product_id', 'woocommerce_id');
    }
}
