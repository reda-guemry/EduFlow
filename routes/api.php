<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoursePurchaseController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\StripeCheckoutController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;


Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/refresh', [AuthController::class, 'refresh']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);


Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('courses', CourseController::class)->only(['index', 'show']);

    Route::get('/profile', [StudentController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'categories']);



    Route::middleware('teacher')->group(function () {

        Route::apiResource('courses', CourseController::class)->except(['index', 'show']);

        Route::get('/teacher/courses', [CourseController::class, 'myCourses']);
    });

    Route::middleware('student')->group(function () {

        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/{course}', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{course}', [FavoriteController::class, 'destroy']);

        Route::post('/interce/{category}', [StudentController::class, 'store']);


        // Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store']);

        Route::post('/courses/{course}/purchase', [CoursePurchaseController::class, 'store']);

        // Route::get('/purchases/{purchase}', [CoursePurchaseController::class, 'show']);



        Route::post('/purchases/{purchase}/checkout-session', [StripeCheckoutController::class, 'store']);

    });

});



