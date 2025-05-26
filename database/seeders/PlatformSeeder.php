<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Platform;
class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Platform::create([
            'name' => 'woocommerce',
            'account' => '5005360',
            'url' => config('woocommerce.url'),
            'consumer_key' => config('woocommerce.consumer_key'),
            'consumer_secret' => config('woocommerce.consumer_secret'),
            'version' => config('woocommerce.api_version'),
            'timeout' => config('woocommerce.timeout'),
            'ssl_verify' => config('woocommerce.ssl_verify'),
        ]);

    }
}
