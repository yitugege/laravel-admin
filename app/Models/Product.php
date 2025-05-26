<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table = 'products';
    protected $fillable = [
        'woocommerce_id',
        'type',
        'parent_id',
        'name',
        'status',
        'featured',
        'description',
        'short_description',
        'sku',
        'price',
        'regular_price',
        'sale_price',
        'stock_quantity',
        'stock_status',
        'category_id',
        'tags',
        'images',
        'attributes',
        'meta_data'


    ];

    protected $casts = [
        'featured' => 'boolean',
        'tags' => 'array',
        'images' => 'array',
        'attributes' => 'array',
        'meta_data' => 'array',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
