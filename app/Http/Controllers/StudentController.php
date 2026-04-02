<?php

namespace App\Http\Controllers;

use App\Services\StudentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(
        private StudentService $studentService
    ) {
    }


    public function index()
    {
        try {

            $categories = $this->studentService->profile(auth()->user()->id);

            return response()->json([
                'success' => true,
                'data' => $categories,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }


    }

    public function store(Request $request, int $categoryId)
    {
        try {
            $result = $this->studentService->store(auth()->user()->id, $categoryId);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already attached to this category.',
                ], 409);
            }

            return response()->json([
                'success' => true,
                'message' => 'User attached to category successfully.',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


}
