<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class CategoryService
{
    
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ){}

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->getAll();
    }

   
    public function getCategoryById(int $id): Category
    {
        return $this->categoryRepository->findById($id);
    }


    public function createCategory(array $data): Category
    {
        return DB::transaction(function () use ($data): Category {
            return $this->categoryRepository->create($data);
        });
    }

    
    public function updateCategory(int $id, array $data): Category
    {
        return DB::transaction(function () use ($id, $data): Category {
            return $this->categoryRepository->update($id, $data);
        });
    }


    public function deleteCategory(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $category = $this->categoryRepository->findById($id);

            if ($category->courses()->exists()) {
                throw new Exception('Cannot delete a category that has associated courses.');
            }

            return $this->categoryRepository->delete($id);
        });
    }
}
