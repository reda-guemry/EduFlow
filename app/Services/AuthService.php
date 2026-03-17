<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepository $userRepository ,
    )
    {}


    public function register(array $data)
    {
        $user = $this -> userRepository -> create($data) ;

        $token = auth()->login($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

}
