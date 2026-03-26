<?php

namespace App\Http\Controllers;

use App\Services\StripeCheckoutService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class StripeCheckoutController extends Controller
{

    public function __construct(
        private StripeCheckoutService $stripeCheckoutService
    ){}

    public function store(Request $request , int $coursePurchaseId) 
    {
        $user = auth()->user() ; 

        try {
            $result = $this->stripeCheckoutService->createCheckoutSession($user, $coursePurchaseId);

            return response()->json([
                'message' => 'Checkout session created successfully.',
                'data' => $result,
            ], 201);
            
        }catch(ModelNotFoundException $e) {
            return response()->json(['error' => 'Course purchase not found.'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }

    }


}
