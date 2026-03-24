<?php


namespace App\Repositories;

use App\Models\Enrollment;

class EnrollmentRepository implements EnrollmentRepositoryInterface
{
    public function isUserEnrolled(int $userId, int $courseId): bool
    {
        return Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }

    
    public function createEnrollment(array $data): Enrollment
    {
        return Enrollment::create($data);
    }
}
