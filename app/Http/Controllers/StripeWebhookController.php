<?php

namespace App\Http\Controllers;

use App\Services\EnrollmentService;
use Illuminate\Http\Request;

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
            
        }

    }

}
