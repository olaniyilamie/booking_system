<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

    public function createToken(User $user, string $deviceName = 'api'): string
    {
        $newToken = $user->createToken($deviceName);
        $accessToken = $newToken->accessToken;
        $accessToken->expires_at = now()->addHour();
        $accessToken->save();

        return $newToken->plainTextToken;
    }

    public function logoutCurrent($user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
