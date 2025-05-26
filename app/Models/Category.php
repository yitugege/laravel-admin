<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    //
    protected $table = 'categories';
    protected $fillable = [
        'id',
        'name',
        'description',
        'image',
        'parent'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
