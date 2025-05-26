<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WooCommerce API Configuration
    |--------------------------------------------------------------------------
    |
    | 这里配置 WooCommerce API 的连接信息
    |
    */

    'url' => env('WOOCOMMERCE_URL'),
    'consumer_key' => env('WOOCOMMERCE_CONSUMER_KEY'),
    'consumer_secret' => env('WOOCOMMERCE_CONSUMER_SECRET'),
    'api_version' => env('WOOCOMMERCE_API_VERSION'),
    'timeout' => env('WOOCOMMERCE_TIMEOUT'),
    'ssl_verify' => env('WOOCOMMERCE_SSL_VERIFY'),

];
