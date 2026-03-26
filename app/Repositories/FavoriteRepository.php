<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    
    public function getUserFavorites(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        $user = User::findOrFail($userId);
        return $user->wishlist()->paginate($perPage);
    }

    
    public function addFavorite(int $userId, int $courseId): bool
    {
        $user = User::findOrFail($userId);
        
        $user->wishlist()->syncWithoutDetaching([$courseId]);
        
        return true;
    }


    public function removeFavorite(int $userId, int $courseId): bool
    {
        $user = User::findOrFail($userId);
        
        return (bool) $user->wishlist()->detach($courseId);
    }

    
    public function isFavorited(int $userId, int $courseId): bool
    {
        $user = User::findOrFail($userId);
        return $user->wishlist()->where('course_id', $courseId)->exists();
    }
}
