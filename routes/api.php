<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController; // 🚨 تأكد من صحة مسار الكنترولر

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