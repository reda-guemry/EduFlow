<?php

namespace App\Repositories\Interfaces;

use App\Models\CoursePurchase;

interface CoursePurchaseRepositoryInterface
{
    public function purchaseCourse(array $data): CoursePurchase ;

    public function isCoursePurchased(int $userId, int $courseId): bool;

    public function getCoursePurchaseById(int $coursePurchaseId): ?CoursePurchase;

    public function update(int $coursePurchaseId, array $data): CoursePurchase;

}
