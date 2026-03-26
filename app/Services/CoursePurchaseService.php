<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Repositories\Interfaces\CoursePurchaseRepositoryInterface;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CoursePurchaseService
{


    public function __construct(
        private EnrollmentRepositoryInterface $enrollmentRepository,
        private GroupRepositoryInterface $groupRepository,
        private CoursePurchaseRepositoryInterface $coursePurchaseRepository,

    ) {
    }


    public function createPurchase(int $userId, int $courseId, string $paymentMethodId): array
    {
        $user = User::findOrFail($userId);
        $course = Course::findOrFail($courseId);

        if ($this->coursePurchaseRepository->isCoursePurchased($userId, $courseId)) {
            throw new Exception('User has already purchased this course.');
        }

        return DB::transaction(function () use ($user, $course, $userId, $courseId, $paymentMethodId): array {

            $coursePurchase = $this->coursePurchaseRepository->purchaseCourse([
                'user_id' => $userId,
                'course_id' => $courseId,
                'amount' => (int) ($course->price * 100),
                'currency' => 'mad',
            ]);

            return [
                'coursePurchase' => $coursePurchase,
            ];
        });
    }
    // public function pursacheget(int $i)
    // {
    //     $user = $request->user();

    //     if ($purchase->user_id !== $user->id) {
    //         return response()->json([
    //             'message' => 'Unauthorized.'
    //         ], 403);
    //     }

    //     return response()->json([
    //         'purchase' => $purchase,
    //     ]);
    // }


    // private function processStripePayment(User $user, Course $course, string $paymentMethodId)
    // {
    //     try {
    //         Stripe::setApiKey(config('services.stripe.secret'));

    //         $paymentIntent = PaymentIntent::create([
    //             'amount' => (int) ($course->price * 100),
    //             'currency' => 'mad',
    //             'payment_method' => $paymentMethodId,
    //             'confirm' => true,
    //             'automatic_payment_methods' => [
    //                 'enabled' => true,
    //                 'allow_redirects' => 'never',
    //             ],
    //         ]);


    //         if ($paymentIntent->status === 'succeeded') {
    //             return $paymentIntent->id;
    //         }

    //         throw new Exception("Le paiement n'a pas pu être confirmé. Statut: " . $paymentIntent->status);

    //     } catch (CardException $e) {
    //         throw new Exception("Erreur de carte : " . $e->getError()->message);
    //     } catch (Exception $r) {
    //         throw new Exception("Erreur de paiement : " . $r->getMessage());
    //     }

    // }
}
