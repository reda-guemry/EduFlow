<?php


namespace App\Repositories\Interfaces;

use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface FavoriteRepositoryInterface
{
    public function getUserFavorites(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function addFavorite(int $userId, int $courseId): bool;

    
    public function removeFavorite(int $userId, int $courseId): bool;

    public function isFavorited(int $userId, int $courseId): bool;
}
