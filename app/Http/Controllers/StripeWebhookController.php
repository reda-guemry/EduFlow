<?php

namespace App\Http\Controllers;

use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{

    public function __construct(
        private EnrollmentService $enrollmentService , 
    ){}


    public function handleWebhook(Request $request) 
    {
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent() ; 
        $sig_header = $request->header('Stripe-Signature') ; 
        $event = null ; 

        try {
            $event = Webhook::constructEvent($payload , $sig_header , $endpoint_secret) ;
        }catch(UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }catch(SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

    }

}
