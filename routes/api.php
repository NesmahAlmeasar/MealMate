<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ChronicDiseaseController;
use App\Http\Controllers\Api\DietController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\NutritionistController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AccountingController;
use App\Http\Controllers\Api\PantryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==================== Public APIs (غير محتاجة لتسجيل دخول) ====================

// 1. APIs خاصة بالمطاعم
Route::prefix('restaurants')->group(function () {
    Route::get('/', [RestaurantController::class, 'index']);
    Route::get('/by-diet/{dietId}', [RestaurantController::class, 'getRestaurantsByDiet']); // API جديد: المطاعم حسب الحمية
    Route::get('/{id}', [RestaurantController::class, 'show']);
    Route::get('/{restaurantId}/categories', [RestaurantController::class, 'getCategories']);
    Route::get('/{restaurantId}/categories/{categoryId}/meals', [RestaurantController::class, 'getCategoryMeals']);
    Route::get('/{restaurantId}/meals', [RestaurantController::class, 'meals']);
    Route::get('/{restaurantId}/diets/{dietId}/meals', [RestaurantController::class, 'getMealsByRestaurantAndDiet']); // API 10
    Route::get('/{restaurantId}/categories/{categoryId}/diets/{dietId}/meals', [RestaurantController::class, 'getMealsByRestaurantCategoryAndDiet']); // API 11
    Route::get('/nearby', [RestaurantController::class, 'nearbyRestaurants']);
});

// 2. APIs خاصة بالوجبات
Route::prefix('meals')->group(function () {
    Route::get('/{id}', [MealController::class, 'show']); // API 7: تفاصيل الوجبة
    Route::get('/{id}/description', [MealController::class, 'getDescription']); // API 8: وصف الوجبة
    Route::get('/{id}/full-details', [MealController::class, 'getFullDetails']); // API 12: تفاصيل كاملة
    Route::get('/search', [MealController::class, 'search']); // API 9: البحث عن وجبات
});

// 3. APIs الحميات
Route::prefix('diets')->group(function () {
    Route::get('/', [DietController::class, 'index']);
    Route::get('/{id}', [DietController::class, 'show']);
    Route::get('/{dietId}/meals', [RestaurantController::class, 'getMealsByDiet']); // API 13
});

// Registration API Routes
Route::prefix('register')->group(function () {
    Route::post('/email', [RegisterController::class, 'registerWithEmail']);
    Route::post('/phone', [RegisterController::class, 'registerWithPhone']);
    Route::post('/body-data', [RegisterController::class, 'saveBodyData']);
    Route::post('/lifestyle-data', [RegisterController::class, 'saveLifestyleData']);
    Route::get('/health-options', [RegisterController::class, 'getHealthOptions']);
    Route::post('/health-data', [RegisterController::class, 'saveHealthData']);
});

// ==================== Protected APIs (تتطلب تسجيل دخول) ====================

// ==================== Protected APIs (تتطلب تسجيل دخول) ====================

Route::middleware('auth:sanctum')->group(function () {
    // 4. APIs السلات المتعددة (Multi-Cart System)
    Route::get('/cart/nutrition', [CartController::class, 'getCartNutrition']); // [NEW] Cart Nutrition Summary

    Route::prefix('carts')->group(function () {
        Route::get('/', [CartController::class, 'getAllCarts']); // عرض جميع السلات
        Route::get('/{cartId}', [CartController::class, 'getCartDetails']); // تفاصيل سلة محددة
        Route::put('/{cartId}/location', [CartController::class, 'updateCartLocation']); // تحديث موقع السلة
    });

    // 5. APIs خاصة بالسلة الواحدة (Single Cart Operations)
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']); // API 10: عرض السلة النشطة
        Route::post('/add-item', [CartController::class, 'addItem']); // API 11: إضافة وجبة
        Route::put('/update-item/{cartItemId}', [CartController::class, 'updateItem']); // API 12: تحديث الكمية
        Route::delete('/remove-item/{cartItemId}', [CartController::class, 'removeItem']); // API 13: حذف عنصر
        Route::delete('/clear', [CartController::class, 'clearCart']); // API 14: تفريغ السلة
        Route::post('/confirm', [CartController::class, 'confirmOrder']); // API 15: تأكيد الطلب
    });
    // 6. APIs إدارة المواقع (Locations Management)
    Route::prefix('locations')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\LocationController::class, 'index']); // عرض جميع المواقع
        Route::post('/', [\App\Http\Controllers\Api\LocationController::class, 'store']); // إضافة موقع جديد
        Route::put('/{locationId}', [\App\Http\Controllers\Api\LocationController::class, 'update']); // تحديث موقع
        Route::delete('/{locationId}', [\App\Http\Controllers\Api\LocationController::class, 'destroy']); // حذف موقع
    });

    // 7. APIs الملف الشخصي (Profile Management)
    Route::prefix('profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProfileController::class, 'show']); // عرض الملف الشخصي
        Route::put('/', [\App\Http\Controllers\Api\ProfileController::class, 'update']); // تحديث الملف الشخصي
    });

    // 8. APIs إعدادات المستخدم (Diet & Location)
    Route::prefix('client')->group(function () {
        Route::get('/diet', [\App\Http\Controllers\Api\ProfileController::class, 'getDiet']);
        Route::post('/diet', [\App\Http\Controllers\Api\ProfileController::class, 'updateDiet']);
        Route::get('/location', [\App\Http\Controllers\Api\ProfileController::class, 'getLocation']);
        Route::post('/location', [\App\Http\Controllers\Api\ProfileController::class, 'updateLocation']);
        Route::get('/locations', [\App\Http\Controllers\Api\ProfileController::class, 'getAllLocations']); // [NEW] Get All Locations
    });

    // معلومات المستخدم
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::get('/unread', [\App\Http\Controllers\Api\NotificationController::class, 'unread']);
        Route::post('/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']);
        Route::post('/create-reminder', [\App\Http\Controllers\Api\NotificationController::class, 'createReminder']);
    });

    // AI Diet Suggestion Routes
    Route::prefix('ai-diet')->group(function () {
        Route::get('/check-completion', [\App\Http\Controllers\Api\AiDietController::class, 'checkProfileCompletion']);
        Route::post('/suggest-diet', [\App\Http\Controllers\Api\AiDietController::class, 'suggestDiet']);
        Route::post('/generate-private-diet', [\App\Http\Controllers\Api\AiDietController::class, 'generatePrivateDiet']);
        Route::post('/set-diet', [\App\Http\Controllers\Api\AiDietController::class, 'setDiet']);
    });
});

// Consultation Routes (Protected)

// Nutritionists
Route::get('/nutritionists', [NutritionistController::class, 'index']);
Route::get('/nutritionists/{id}', [NutritionistController::class, 'show']);
Route::get('/consultation-types', [App\Http\Controllers\Api\ConsultationTypeController::class, 'index']); // [NEW] Types

Route::middleware('auth:sanctum')->group(function () {

    // Consultations
    Route::get('/consultations', [App\Http\Controllers\Api\ConsultationController::class, 'index']); // [NEW] List
    Route::get('/consultations/eligibility/{nutritionistId}', [App\Http\Controllers\Api\ConsultationController::class, 'checkEligibility']);
    Route::post('/consultations/start', [App\Http\Controllers\Api\ConsultationController::class, 'store']);
    Route::post('/consultations/confirm', [App\Http\Controllers\Api\ConsultationController::class, 'confirmPayment']);
    Route::post('/consultations/{id}/close', [App\Http\Controllers\Api\ConsultationController::class, 'close']); // [NEW] Close

    // Chat (Ensure these exist if not already wrapped in auth elsewhere)
    Route::get('/chat/conversations', [App\Http\Controllers\ChatController::class, 'getConversations']);
    Route::get('/chat/messages/{otherUserId}', [App\Http\Controllers\ChatController::class, 'getMessages']);
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage']);
    Route::get('/chat/can-send/{userId}', [App\Http\Controllers\ChatController::class, 'canSendMessage']); // [NEW] Check Status
});

Route::prefix('chronic-diseases')->group(function () {
    Route::get('/', [ChronicDiseaseController::class, 'index']); // جميع الأمراض
    Route::get('/client/{clientId}', [ChronicDiseaseController::class, 'clientDiseases']); // أمراض مستخدم
});

// ==================== APIs تسجيل الدخول ====================
Route::post('/login', [AuthController::class, 'login']);

// في أي مكان في ملف routes/api.php
Route::post('/validate-token', function (Request $request) {
    $user = Auth::guard('sanctum')->user();

    return response()->json([
        'is_valid' => $user ? true : false,
        'user_id' => $user ? $user->id : null,
        'user_name' => $user ? $user->name : null,
        'token_preview' => $request->bearerToken() ? substr($request->bearerToken(), 0, 20) . '...' : 'No token',
        'headers_received' => [
            'authorization' => $request->header('authorization'),
            'accept' => $request->header('accept'),
        ],
    ]);
});

// ==================== BAS Gateway Payment Routes ====================
// Public webhook (Server-to-Server from BAS, no auth needed)
Route::post('/payments/bas/webhook', [PaymentController::class, 'handleWebhook']);
Route::get('/payments/bas/mock-pay/{transactionId}', [PaymentController::class, 'mockPaySuccess']);
Route::get('/payments/status/{transactionId}', [PaymentController::class, 'checkStatus']);

// v1 aliases for payment endpoints
Route::post('/v1/payments/bas/webhook', [PaymentController::class, 'handleWebhook']);
Route::get('/v1/payments/status/{transactionId}', [PaymentController::class, 'checkStatus']);

// Protected payment routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/payments/checkout', [PaymentController::class, 'initiateCheckout']);
    Route::post('/v1/payments/checkout', [PaymentController::class, 'initiateCheckout']);

    // ==================== Accounting Routes (All Users) ====================
    Route::get('/wallet', [AccountingController::class, 'getWallet']);
    Route::get('/ledger', [AccountingController::class, 'getLedgerEntries']);
    Route::post('/payout/request', [AccountingController::class, 'requestPayout']);

    // ==================== Admin-Only Accounting Routes ====================
    Route::middleware('role:admin')->group(function () {
        Route::get('/accounting/summary', [AccountingController::class, 'getFinancialSummary']);
        Route::post('/accounting/penalty', [AccountingController::class, 'applyPenalty']);
    });

    // ==================== Pantry Smart Engine Routes ====================
    Route::post('/pantry/suggest', [PantryController::class, 'suggestMeals']);
});
