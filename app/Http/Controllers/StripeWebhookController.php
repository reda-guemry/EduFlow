<?php

namespace App\Http\Controllers;

use App\Services\CoursePurchaseService;
use App\Services\EnrollmentService;
use Exception;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{

    public function __construct(
        private EnrollmentService $enrollmentService,
        private CoursePurchaseService $coursePurchaseService
    ) {
    }


    public function handleWebhook(Request $request)
    {
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }


        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                $purchaseId = $session->metadata->purchase_id ?? $session->client_reference_id ?? null;

                if (!$purchaseId) {
                    return response()->json(['error' => 'Missing purchase reference'], 400);
                }

                try {
                    $coursePurchase = $this->coursePurchaseService->markAsCompleted($purchaseId);

                    $result = $this->enrollmentService->activateEnrollment($coursePurchase->id);

                    return response('Webhook handled', 200);
                } catch (Exception $e) {
                    return response('Webhook handled', 500);
                }
                break ;
            default:
                return response('Event type not handled', 200); 
        }

    }

}
