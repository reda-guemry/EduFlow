<?php


namespace App\Repositories\Interfaces;

use App\Models\Enrollment;

interface EnrollmentRepositoryInterface
{
    
    public function isUserEnrolled(int $userId, int $courseId): bool;

    
    public function createEnrollment(array $data): Enrollment;
}
