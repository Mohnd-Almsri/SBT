<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\CourseRunController;
use App\Http\Controllers\Api\V1\BookingRequestController;

Route::prefix('v1')->group(function () {

    // Public catalog (بدون auth)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/featured', [CourseController::class, 'showFeaturedCourses']);
    Route::get('/courses/{course}', [CourseController::class, 'show']);

    Route::get('/course-runs', [CourseRunController::class, 'index']);
    Route::get('/time',function (){
      return now();
    });
    Route::get('/course-runs/{courseRun}', [CourseRunController::class, 'show']);

    // Booking (بدون auth)
    Route::post('/booking-requests', [BookingRequestController::class, 'store']);
});
