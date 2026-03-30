<?php


namespace App\Http\Controllers;

use App\Http\Resources\CoursePurchaseResource;
use App\Services\CoursePurchaseService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoursePurchaseController extends Controller
{
    
    public function __construct(
        private CoursePurchaseService $enrollmentService
    ){}


    public function store(Request $request , int $courseId): JsonResponse
    {

        try {
            $result = $this->enrollmentService->createPurchase(auth('api')->user()->id, $courseId );

            return response()->json([
                'success' => true,
                'message' => 'Course purchase created successfully.',
                'data' => new CoursePurchaseResource($result['coursePurchase']),
            ], 201);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Course or user not found.',
            ], 404 );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
    }
}
