<?php


namespace App\Http\Controllers;

use App\Services\EnrollmentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnrollmentController extends Controller
{
    
    public function __construct(
        private EnrollmentService $enrollmentService
    ){}


    public function store(int $courseId): JsonResponse
    {

        try {
            $result = $this->enrollmentService->enrollStudent(auth('api')->user()->id, $courseId);

            return response()->json([
                'success' => true,
                'message' => 'Successfully enrolled in the course.',
                'data' => [
                    'enrollment_id' => $result['enrollment']->id,
                    'course_id' => $result['enrollment']->course_id,
                    'group_name' => $result['group']->name,
                    'group_id' => $result['group']->id,
                    'payment_id' => $result['enrollment']->stripe_payment_id,
                    'status' => $result['enrollment']->status,
                ],
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
