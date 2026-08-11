<?php

namespace App\Http\Controllers\Api;

use App\Auth\PermissionMap;
use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * Authenticate a user and create a token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            if ($user) {
                LoginLog::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'failed',
                    'logged_in_at' => now(),
                ]);
            }
            
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Your account has been deactivated.'], 403);
        }

        $user->load(['branch:id,name', 'tenant']);

        LoginLog::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success',
            'logged_in_at' => now(),
        ]);

        $abilities = class_exists(PermissionMap::class) && method_exists(PermissionMap::class, 'tokenAbilities') 
            ? PermissionMap::tokenAbilities($user->role) 
            : ['*'];

        // Enforce session concurrency: prevent a cashier from being logged in on multiple devices.
        if ($user->role === 'cashier') {
            $user->tokens()->delete();
        }
            
        $token = $user->createToken($request->device_name, $abilities)->plainTextToken;

        $permissions = class_exists(PermissionMap::class) && method_exists(PermissionMap::class, 'forRole')
            ? PermissionMap::forRole($user->role)
            : [];

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id,
                'tenant' => $user->tenant,
                'branch_id' => $user->branch_id,
                'branch' => $user->branch,
                'role' => $user->role,
                'is_active' => $user->is_active,
                'permissions' => $permissions,
            ],
        ]);
    }

    /**
     * Revoke the user's current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Logged out.']);
    }

    /**
     * Get the authenticated user's information.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $user->load(['branch', 'tenant']);

        $permissions = class_exists(PermissionMap::class) && method_exists(PermissionMap::class, 'forRole')
            ? PermissionMap::forRole($user->role)
            : [];

        $userData = $user->toArray();
        $userData['permissions'] = $permissions;

        return response()->json($userData);
    }
}
