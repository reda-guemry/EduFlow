<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\CoursePurchaseRepositoryInterface;
use Exception;
use Stripe\Stripe;
use Stripe\Checkout\Session ; 


class StripeCheckoutService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CoursePurchaseRepositoryInterface $coursePurchaseRepository
    ) {
    }

    public function createCheckoutSession(int $userId, int $coursePurchaseId)
    {
        $coursePurchase = $this->coursePurchaseRepository->getCoursePurchaseById($coursePurchaseId);

        if ($userId !== $coursePurchase->user_id) {
            throw new Exception('Unauthorized access to course purchase.', 403);
        }

        if ($coursePurchase->status !== 'pending') {
            throw new Exception('Course purchase is not in a pending state.', 403);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'client_reference_id' => $coursePurchase->id,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $coursePurchase->currency,
                        'product_data' => [
                            'name' => $coursePurchase->course->title,
                        ],
                        'unit_amount' => (int) $coursePurchase->amount,
                    ],
                    'quantity' => 1,
                ]
            ],
            'success_url' => env('FRONTEND_URL') . '/checkout/success',
            'cancel_url' => env('FRONTEND_URL') . '/checkout/cancel' ,
            'metadata' => [
                'course_purchase_id' => $coursePurchase->id,
                'user_id' => $userId,
                'course_id' => $coursePurchase->course_id,
            ],
        ]);

        $coursePurchase = $this->coursePurchaseRepository->update($coursePurchase->id, [
            'stripe_session_id' => $session->id,
        ]);

        return [
            'checkout_url' => $session->url,
            'course_purchase' => $coursePurchase,
            'session_id' => $session->id,
        ];


    }


}
