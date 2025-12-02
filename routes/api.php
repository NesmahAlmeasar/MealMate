<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController; // 🚨 تأكد من صحة مسار الكنترولر
use App\Http\Controllers\Api\DietController;
use App\Http\Controllers\Api\RestaurantController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// مسارات تسجيل الدخول بـ OTP
// 1. المرحلة الأولى: طلب رمز التحقق
Route::post('/login/request-code', [AuthController::class, 'requestCode']); // ⭐️ هذا هو المسار المطلوب

// 2. المرحلة الثانية: التحقق من الرمز المدخل
Route::post('/login/verify-code', [AuthController::class, 'verifyCode']);

// مسار التحقق من وجود المستخدم
Route::post('/user/check-existence', [AuthController::class, 'checkExistence']);

// مسار افتراضي لاختبار اتصال الـ API (محمي بـ Sanctum Token)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// المسار القديم الذي ربما لن تحتاج إليه إذا كنت تستخدم OTP
Route::post('/login', [AuthController::class, 'login']);

// Diets API
Route::get('/diets', [DietController::class, 'index']);
Route::get('/diets/{id}', [DietController::class, 'show']);

// Restaurants API
Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurants/{id}/meals', [RestaurantController::class, 'meals']);