<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->get('roleName')) {
                $role = Role::where('name', $request->get('roleName'))->first();

                if (!$role) {
                    return response()->json([
                        'code' => 404,
                        'message' => '角色不存在',
                        'data' => null
                    ], 404);
                }

                $permissions = $role->permissions;

                $data = [
                    'roleName' => $role->name,
                    'id' => $role->id,
                    'permissions' => $permissions->pluck('name')->toArray()
                ];

                return response()->json([
                    'code' => 0,
                    'message' => 'success',
                    'data' => $data
                ]);
            }

            // 获取所有角色及其权限
            $roles = Role::with('permissions')->get();

            $data = $roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->toArray()
                ];
            });

            return response()->json([
                'code' => 0,
                'message' => 'success',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('获取角色权限失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'code' => 500,
                'message' => '服务器错误',
                'data' => null
            ], 500);
        }
    }
}
