<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\FavoriteRepositoryInterface;
use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FavoriteService
{
    
    public function __construct(
        private FavoriteRepositoryInterface $favoriteRepository
    ){}

  
    public function getUserFavorites(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->favoriteRepository->getUserFavorites($userId, $perPage);
    }

    public function addFavorite(int $userId, int $courseId): bool
    {
        Course::findOrFail($courseId);

        if ($this->favoriteRepository->isFavorited($userId, $courseId)) {
            return false;
        }

        return $this->favoriteRepository->addFavorite($userId, $courseId);
    }

    
    public function removeFavorite(int $userId, int $courseId): bool
    {
        Course::findOrFail($courseId);

        return $this->favoriteRepository->removeFavorite($userId, $courseId);
    }

    public function isFavorited(int $userId, int $courseId): bool
    {
        return $this->favoriteRepository->isFavorited($userId, $courseId);
    }
}
