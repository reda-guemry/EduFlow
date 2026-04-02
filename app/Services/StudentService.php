<?php 


namespace App\Services;

use App\Models\Category;
use App\Repositories\UserRepository;



class StudentService
{

    public function __construct(
        private UserRepository $userRepository
    ){}

    public function profile(int $userId)
    {
        return $this->userRepository->getUserDetails($userId) ;
    }

    public function store(int $userId, int $categoryId)
    {
        Category::findOrFail($categoryId);

        if ($this->userRepository->isAttachedToCategory($userId, $categoryId)) {
            return false ;
        }

        return $this->userRepository->attachCategory($userId, $categoryId);

    }
    
} 