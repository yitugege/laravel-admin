<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 重置角色和权限的缓存
        app('cache')->forget('spatie.permission.cache');



        // 创建权限
        $permissions = [
            'manage_users',
            'create_posts',
            'edit_posts',
            'delete_posts',
            'view_posts',
            'can_sync_products',
            'can_sync_orders',
        ];
         foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }



       // 创建角色并绑定权限
        Role::firstOrCreate(['name' => 'admin'])
            ->givePermissionTo($permissions);

        Role::firstOrCreate(['name' => 'manager'])
            ->givePermissionTo([
                'create_posts',
                'edit_posts',
                'delete_posts',
                'view_posts',
                'can_sync_products',
                'can_sync_orders',
            ]);

        Role::firstOrCreate(['name' => 'user'])
            ->givePermissionTo(['view_posts']);
    }

}
