<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $admin = User::firstOrCreate(
            ['email' => 'admin@123.com'],
            [
                'username' => 'admin',
                'name' => 'admin',
                'email' => 'admin@123.com',
                'password' => Hash::make('admin'),
                //     'abilities' => '["admin","manager"]',
            ]
        );
        $admin->assignRole('admin');
        $manager = User::firstOrCreate(
            ['email' => 'manager@123.com'],
            [
                'username' => 'manager',
                'name' => 'manager',
                'email' => 'manager@123.com',
                'password' => Hash::make('manager'),
                //    'abilities' => '["manager"]',
            ]
        );
        $manager->assignRole('manager');
        // 定义你要指定的 Token（格式必须为 "id|random_string"）
        $plainTextToken = '1|fb3KheNVJ0d1XU8sGAoKo9sW21bZadk5mZ4CrcCDa8af5d3b';

        $token = PersonalAccessToken::firstOrCreate(
            ['id' => 1],
            [
                'tokenable_id' => 1,
                'tokenable_type' => 'App\Models\User',
                'name' => 'auth_token',
                'token' => Hash::make('sha256,' . $plainTextToken),
                'abilities' => '["admin","manager"]',
            ]
        );
    }
}
