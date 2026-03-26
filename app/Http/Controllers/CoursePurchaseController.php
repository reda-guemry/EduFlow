<?php


namespace App\Http\Controllers;

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
            $result = $this->enrollmentService->enrollStudent(auth('api')->user()->id, $courseId , $request->payment_method_id);

            
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
