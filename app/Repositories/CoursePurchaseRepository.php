<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CoursePurchaseRepositoryInterface;

use App\Models\CoursePurchase ; 

class CoursePurchaseRepository implements CoursePurchaseRepositoryInterface
{
   

    public function purchaseCourse(array $data): CoursePurchase
    {
        return CoursePurchase::create($data) ; 
    }

    public function isCoursePurchased(int $userId, int $courseId): bool
    {
        return CoursePurchase::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }

    public function getCoursePurchaseById(int $coursePurchaseId): ?CoursePurchase
    {
        return CoursePurchase::findOrFail($coursePurchaseId);
    }

    public function update(int $coursePurchaseId, array $data): CoursePurchase
    {
        $coursePurchase = CoursePurchase::findOrFail($coursePurchaseId);
        $coursePurchase->update($data);
        return $coursePurchase;
    }
  

}
