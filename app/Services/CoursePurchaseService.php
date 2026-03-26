<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\User;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CoursePurchaseService
{
 

    public function __construct(
        private EnrollmentRepositoryInterface $enrollmentRepository,
        private GroupRepositoryInterface $groupRepository
    ) {}

   
    public function createPurchase(int $userId, int $courseId , string $paymentMethodId): array
    {
        $user = User::findOrFail($userId);
        $course = Course::findOrFail($courseId);

        if ($this->enrollmentRepository->isUserEnrolled($userId, $courseId)) {
            throw new Exception('User is already enrolled in this course.');
        }

        return DB::transaction(function () use ($user, $course, $userId, $courseId , $paymentMethodId): array {

            $paymentId = $this->processStripePayment($user, $course , $paymentMethodId);

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

   
    private function processStripePayment(User $user, Course $course ,string $paymentMethodId)
    {
        try { 
            Stripe::setApiKey(config('services.stripe.secret')) ; 

            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($course->price * 100) , 
                'currency' => 'mad' , 
                'payment_method' => $paymentMethodId , 
                'confirm' => true , 
                'automatic_payment_methods' => [
                    'enabled' => true , 
                    'allow_redirects' => 'never' , 
                ] , 
            ]) ; 


            if ($paymentIntent->status === 'succeeded') {
                return $paymentIntent->id; 
            }

            throw new Exception("Le paiement n'a pas pu être confirmé. Statut: " . $paymentIntent->status);

        }catch (CardException $e) { 
            throw new Exception("Erreur de carte : " . $e->getError()->message) ; 
        }catch (Exception $r) { 
            throw new Exception("Erreur de paiement : " . $r->getMessage());
        }

    }
}
