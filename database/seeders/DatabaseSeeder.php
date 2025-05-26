<?php

namespace Database\Seeders;
use App\Models\Platform;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            PlatformSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
        ]);
    }
}
