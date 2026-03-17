<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {}

    public function create(array $data)
    {
        return User::create($data) ;
    }


}
