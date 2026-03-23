<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:api')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('courses', CourseController::class)->only(['index', 'show']);

    Route::middleware('teacher')->group(function () {

        Route::apiResource('courses', CourseController::class)->except(['index', 'show']);

    });

    Route::middleware('student')->group(function () {
        
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/{course}', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{course}', [FavoriteController::class, 'destroy']);

    });

});



