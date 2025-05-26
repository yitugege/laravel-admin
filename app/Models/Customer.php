<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_id',
        'email',
        'first_name',
        'last_name',
        'phone',
        'address',
        'total_spent',
        'order_count',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'address' => 'array',
    ];

}

