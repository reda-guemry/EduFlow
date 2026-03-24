<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;


class CourseController extends Controller
{


    public function __construct(
        private CourseService $courseService
    ){}

    
    
    public function index(): JsonResponse
    {
        $courses = $this->courseService->getAll();

        return response() -> json([
            'courses' => $courses , 
        ], 200) ;
    }

    public function store(StoreCourseRequest $request): CourseResource
    {
        $validated = $request->validated();
        $validated['teacher_id'] =  auth('api')->user()->id;

        $course = $this->courseService->create($validated);

        return new CourseResource($course);
    }

    public function show(int $id): CourseResource
    {
        $course = $this->courseService->getById($id);
        return new CourseResource($course);
    }

    public function update(UpdateCourseRequest $request, int $id): CourseResource|JsonResponse
    {
        $validated = $request->validated();

        try {
            $course = $this->courseService->update($id, auth('api')->user()->id, $validated);
            return new CourseResource($course);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->courseService->delete($id, auth('api')->user()->id);
            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully.',
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }
}
