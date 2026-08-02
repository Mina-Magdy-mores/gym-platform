<?php

namespace Modules\User\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Register a new user and assign default role.
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign the default role for members
        $user->assignRole('member');

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
        ];
    }

    /**
     * Attempt login and issue access token.
     */
    public function login(array $credentials): ?array
    {
        if (! Auth::attempt($credentials)) {
            return null;
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();

        // Revoke older tokens to prevent session hijacking
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
        ];
    }

    /**
     * Revoke current user access token.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}