<?php

namespace App\Services;

use App\Models\CoursePurchase;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use DB;
use Exception;

class EnrollmentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private EnrollmentRepositoryInterface $enrollmentRepository , 
        private GroupRepositoryInterface $groupRepository , 
    ){}


    public function activateEnrollment(CoursePurchase $coursePurchase) 
    {
    
        return DB::transaction(function() use ($coursePurchase) {
            $alreadyEnrolled = $this->enrollmentRepository->isUserEnrolled($coursePurchase->user_id, $coursePurchase->course_id); 
            if ($alreadyEnrolled) {
                throw new Exception('User is already enrolled in this course.' , 400 );
            }

            $enrollment = $this->enrollmentRepository->createEnrollment([
                'user_id' => $coursePurchase->user_id,
                'course_id' => $coursePurchase->course_id,
                'status' => 'active',
            ]);

            $group = $this->groupRepository->findAvailableGroupForCourse($coursePurchase->course_id);

            if (!$group) {
                $groupCount = $this->groupRepository->countGroupsForCourse($coursePurchase->course_id);
                $groupName = 'Groupe ' . ($groupCount + 1);
                $group = $this->groupRepository->createGroup($coursePurchase->course_id, $groupName);
            }

            $this->groupRepository->addStudentToGroup($group->id, $coursePurchase->user_id);

            
        });

    }



}
