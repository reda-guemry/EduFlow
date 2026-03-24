<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\User;
use App\Repositories\EnrollmentRepositoryInterface;
use App\Repositories\GroupRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
   
    public function __construct(
        private EnrollmentRepositoryInterface $enrollmentRepository,
        private GroupRepositoryInterface $groupRepository
    ) {}

   
    public function enrollStudent(int $userId, int $courseId): array
    {
        $user = User::findOrFail($userId);
        $course = Course::findOrFail($courseId);

        if ($this->enrollmentRepository->isUserEnrolled($userId, $courseId)) {
            throw new Exception('User is already enrolled in this course.');
        }

        return DB::transaction(function () use ($user, $course, $userId, $courseId): array {

            $paymentId = $this->processStripePayment($user, $course);

            $enrollment = $this->enrollmentRepository->createEnrollment([
                'user_id' => $userId,
                'course_id' => $courseId,
                'stripe_payment_id' => $paymentId,
                'status' => 'active',
            ]);

            $group = $this->groupRepository->findAvailableGroupForCourse($courseId);

            if (!$group) {
                $groupCount = $this->groupRepository->countGroupsForCourse($courseId);
                $groupName = 'Groupe ' . ($groupCount + 1);
                $group = $this->groupRepository->createGroup($courseId, $groupName);
            }

            $this->groupRepository->addStudentToGroup($group->id, $userId);

            return [
                'enrollment' => $enrollment,
                'group' => $group,
            ];
        });
    }

   
    private function processStripePayment(User $user, Course $course): string
    {
        // TODO: Intégrer le vrai Stripe API
        // Pour le moment, retourner une fake transaction ID
        return 'pi_fake_' . uniqid();
    }
}
