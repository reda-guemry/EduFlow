<?php

namespace App\Services;

use App\Repositories\EnrollmentRepository;

class EnrollmentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private EnrollmentRepository $enrollmentRepository
    ){}


    public function activateEnrollmentAfterPayment(int $coursePurchaseId) 
    {
        $enrollment = $this->enrollmentRepository->activateEnrollment($coursePurchaseId) ; 

        return $enrollment ; 
    }



}
