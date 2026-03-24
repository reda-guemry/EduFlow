<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Enrollment;

interface EnrollmentRepositoryInterface
{
    
    public function isUserEnrolled(int $userId, int $courseId): bool;

    
    public function createEnrollment(array $data): Enrollment;
}
