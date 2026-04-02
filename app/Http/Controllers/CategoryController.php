<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function __construct(
        private CategoryService $categoryService
    ){}

    public function categories()
    {
        try{
            $categories = $this->categoryService->getAllCategories();
            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully.',
                'data' => CategoryResource::collection($categories),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories: ' . $e->getMessage(),
            ], 500);
        }

    }
}
