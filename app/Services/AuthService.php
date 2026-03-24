<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Log;
use Str;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthService
{

    public function __construct(
        private UserRepository $userRepository,
    ) {
    }


    public function register(array $data)
    {
        $refreshToken = Str::random(60);

        $data['refresh_token'] = $refreshToken;

        $user = $this->userRepository->create($data);

        $token = JWTAuth::fromUser($user);


        return [
            'user' => $user,
            'token' => $token,
            'refresh_token' => $refreshToken,
        ];
    }

    public function login(array $credentials)
    {
        if (!$token = auth('api')->attempt($credentials)) {
            return null;
        }


        $refreshToken = Str::random(60) ;

        $user = auth('api')->user() ;

        
        
        // return $user; 
        
        $user->update(['refresh_token' => $refreshToken]);
        
        Log::info('User logged in: ' . $user) ;
        return [
            'user' => auth('api')->user(),
            'token' => $token,
            'refresh_token' => $refreshToken,
        ];
    }

    public function refresh(string $refreshTokenFromClient)
    {
        $user = User::where('refresh_token', $refreshTokenFromClient)->first();

        if (!$user) {
            return null;
        }

        $newToken = JWTAuth::fromUser($user);

        $newRefreshToken = Str::random(60);

        $user->update(['refresh_token' => $newRefreshToken]);

        return [
            'token' => $newToken,
            'refresh_token' => $newRefreshToken,
        ];
    }

    public function logout()
    {
        $user = auth('api')->user();

        $token = JWTAuth::getToken();
        if ($token) {
            JWTAuth::invalidate($token);
        }

        if ($user) {
            $user->update(['refresh_token' => null]);
        }

    }

}
