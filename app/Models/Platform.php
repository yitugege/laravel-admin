<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    //
    protected $fillable = [
        'id',
        'name',
        'account',
        'url',
        'consumer_key',
        'consumer_secret',
        'timeout',
        'ssl_verify',
        'version',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'consumer_key' => 'encrypted',
        'consumer_secret' => 'encrypted',
    ];
}
