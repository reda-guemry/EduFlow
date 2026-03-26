<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\CoursePurchaseRepositoryInterface;
use Exception;
use Session;
use Stripe\Stripe;

class StripeCheckoutService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CoursePurchaseRepositoryInterface $coursePurchaseRepository
    ) {
    }

    public function createCheckoutSession(User $user, int $coursePurchaseId)
    {
        $coursePurchase = $this->coursePurchaseRepository->getCoursePurchaseById($coursePurchaseId);

        if ($user->id !== $coursePurchase->user_id) {
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
                        'unit_amount' => $coursePurchase->amount,
                    ],
                    'quantity' => 1,
                ]
            ],
            'success_url' => route('checkout.success', ['coursePurchaseId' => $coursePurchase->id]),
            'cancel_url' => route('checkout.cancel', ['coursePurchaseId' => $coursePurchase->id]),
            'metadata' => [
                'course_purchase_id' => $coursePurchase->id,
                'user_id' => $user->id,
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
