<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Services\FavoriteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{
    public function __construct(
        private FavoriteService $favoriteService
    ){}


    public function index(Request $request): AnonymousResourceCollection
    {

        $perPage = $request->query('per_page', 15);
        $favorites = $this->favoriteService->getUserFavorites(auth('api')->user()->id, $perPage);

        return CourseResource::collection($favorites);
    }


    public function store(Request $request, int $courseId): JsonResponse
    {

        try {
            $added = $this->favoriteService->addFavorite(auth('api')->user()->id, $courseId);

            if (!$added) {
                return response()->json([
                    'success' => false,
                    'message' => 'This course is already in your favorites.',
                ], 409);
            }

            return response()->json([
                'success' => true,
                'message' => 'Course added to favorites successfully.',
            ], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found.',
            ], 404);
        }
    }


    public function destroy(Request $request, int $courseId): JsonResponse
    {
        try {
            $this->favoriteService->removeFavorite( auth('api')->user()->id, $courseId);

            return response()->json([
                'success' => true,
                'message' => 'Course removed from favorites successfully.',
            ], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found.',
            ], 404);
        }

    }

    
}
