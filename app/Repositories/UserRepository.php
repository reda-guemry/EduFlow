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

    public function attachCategory(int $userId, int $categoryId)
    {
        return User::find($userId)->categories()->attach($categoryId) ;
    }

    public function detachCategory(int $userId, int $categoryId)
    {
        return User::find($userId)->categories()->detach($categoryId) ;
    }

    public function isAttachedToCategory(int $userId, int $categoryId): bool
    {
        return User::find($userId)->categories()->where('category_id', $categoryId)->exists() ;
    }

    public function getUserDetails(int $userId)
    {
        return User::with('interests')->where('id', $userId)->first() ;
    }

}
