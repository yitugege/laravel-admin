<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    //登录获取token
    public function login(Request $request)
    {
        $start = microtime(true);

        $user = User::where('email', $request->email)->orWhere('username', $request->username)->first();
        //如果用户不存在或者密码不正确
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        //或者使用username登录
        $user = User::where('username', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid username or password'], 401);
        }
        //如果用户存在并且密码正确
        $token = $user->createToken('auth_token')->plainTextToken;
        $end = microtime(true);
        $timeCost = $end - $start;
        Log::info('登录获取token', ['timeCost' => $timeCost]);
        $permissions = $user->roles->pluck('permissions')->flatten()->pluck('name')->toArray();
        // 返回用户信息和token
        return response()->json([
            'code' => 0,
            'message' => 'success',
            'token' => $token,
            'data' => [
                'username' => $user->username,
                'role' => 'admin',
                'roleId' => 1,
                'permissions' => $permissions
            ],
            'timeCost' => $timeCost
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'string|min:8',
        ]);

        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }

    public function logout(Request $request)
    {
        // 添加调试日志
        Log::info('Logout endpoint hit', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'token' => $request->bearerToken()
        ]);

        try {
            // 记录请求头信息
            Log::info('Logout request headers', [
                'headers' => $request->headers->all(),
                'bearer_token' => $request->bearerToken(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // 获取当前认证用户
            $user = $request->user();

            if (!$user) {
                Log::warning('Logout attempt failed - User not authenticated', [
                    'token' => $request->bearerToken(),
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'code' => 401,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // 删除当前用户的所有令牌
            $user->tokens()->delete();

            Log::info('User logged out successfully', [
                'user_id' => $user->id,
                'username' => $user->username
            ]);

            return response()->json([
                'code' => 0,
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'headers' => $request->headers->all(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'code' => 500,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
