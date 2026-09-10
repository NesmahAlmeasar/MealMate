<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route; // لـ Route::get('/user-role-redirect')

/*
|--------------------------------------------------------------------------
| مسارات الويب (Web Routes)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

// ⭐️⭐️ الحل الأخير: إضافة مسار 'dashboard' الاحتياطي ⭐️⭐️
Route::get('/dashboard', function () {
    // هذا المسار يوجه أي طلب لـ 'dashboard' مباشرة إلى مسار فحص الأدوار
    return redirect()->route('user.role.redirect');
})->name('dashboard'); // 👈 هذا هو الاسم الذي يطلبه Laravel

require __DIR__ . '/auth.php';

// =======================================================
// Shared Routes (للجميع المسجلين) - المسارات المشتركة
// =======================================================
Route::middleware(['auth'])->group(function () {
    // Dashboard الشامل
    Route::get('/shared/dashboard', [\App\Http\Controllers\Shared\DashboardController::class, 'index'])->name('shared.dashboard');

    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\Shared\ProfileController::class, 'show'])->name('shared.profile');
    Route::post('/profile', [\App\Http\Controllers\Shared\ProfileController::class, 'update'])->name('shared.profile.update');

    // Diets Routes - عرض للجميع
    Route::get('/diets', [\App\Http\Controllers\Shared\DietController::class, 'index'])->name('shared.diets');
    Route::get('/diets/{id}', [\App\Http\Controllers\Shared\DietController::class, 'show'])->name('shared.diets.show');

    // Diets CRUD - للأخصائيين فقط
    Route::middleware(['role:Specialist,Nutrition Manager'])->group(function () {
        Route::get('/diets/create/new', [\App\Http\Controllers\Shared\DietController::class, 'create'])->name('shared.diets.create');
        Route::post('/diets', [\App\Http\Controllers\Shared\DietController::class, 'store'])->name('shared.diets.store');
        Route::get('/diets/{id}/edit', [\App\Http\Controllers\Shared\DietController::class, 'edit'])->name('shared.diets.edit');
        Route::put('/diets/{id}', [\App\Http\Controllers\Shared\DietController::class, 'update'])->name('shared.diets.update');
        Route::delete('/diets/{id}', [\App\Http\Controllers\Shared\DietController::class, 'destroy'])->name('shared.diets.destroy');
        Route::post('/diets/{id}/sync-meals', [\App\Http\Controllers\Shared\DietController::class, 'syncMeals'])->name('shared.diets.sync_meals');

        Route::post('/diets/ai-suggest-meals', [\App\Http\Controllers\Shared\DietController::class, 'aiSuggestMeals'])->name('shared.diets.ai_suggest_meals');
    });

    // Dishes/Meals Routes - عرض للجميع
    Route::get('/dishes', [\App\Http\Controllers\Shared\MealController::class, 'index'])->name('shared.dishes');
    Route::get('/dishes/{id}', [\App\Http\Controllers\Shared\MealController::class, 'show'])->name('shared.dishes.show');
    Route::get('/admin/messages', [\App\Http\Controllers\ChatController::class, 'index'])->name('admin.messages');

    // Meals CRUD - للمدير ومدير المطعم
    Route::middleware(['role:Admin,Restaurant Manager'])->group(function () {
        Route::get('/dishes/create/new', [\App\Http\Controllers\Shared\MealController::class, 'create'])->name('shared.dishes.create');
        Route::post('/dishes', [\App\Http\Controllers\Shared\MealController::class, 'store'])->name('shared.dishes.store');
        Route::get('/dishes/{id}/edit', [\App\Http\Controllers\Shared\MealController::class, 'edit'])->name('shared.dishes.edit');
        Route::post('/dishes/{id}', [\App\Http\Controllers\Shared\MealController::class, 'update'])->name('shared.dishes.update');
        Route::delete('/dishes/{id}', [\App\Http\Controllers\Shared\MealController::class, 'destroy'])->name('shared.dishes.destroy');
    });

    // Notifications Routes
    // Notifications Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/check', [\App\Http\Controllers\NotificationController::class, 'check'])->name('notifications.check');
        Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
        Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    });
});

// =======================================================
// 2. مسارات لوحة تحكم المدير (Admin Group) ⭐️ محمية بـ Role Middleware ⭐️
// =======================================================
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->group(function () {

    // لوحة التحكم الرئيسية
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // --- إدارة المستخدمين (CRUD) ---

    // [GET] عرض جدول المستخدمين (الرئيسية)
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');

    // [POST] إضافة مستخدم جديد
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');

    // [DELETE] حذف المستخدم
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // [GET] عرض صفحة تعديل المستخدم (يستخدم المتحكم)
    Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit');

    // [PUT] معالجة التحديث
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');

    // --- إدارة الأدوار (Roles) ---
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);

    // --- إدارة أنواع الاستشارات (Consultation Types) ---
    Route::resource('consultation-types', \App\Http\Controllers\Admin\ConsultationTypeController::class);

    // بروفايل المستخدم
    Route::get('/users/profile/{id}', function ($id) {
        $user = \App\Models\User::with('roles')->findOrFail($id);
        // Also fetch nutritionist data if applicable, though primarily for clients now
        $nutritionist = null;
        if ($user->hasRole('Specialist') || $user->hasRole('Nutrition Manager')) {
            $nutritionist = \App\Models\Nutritionist::with('certificates')->where('nutritionist_id', $id)->first();
        }

        return view('admin.client_profile', compact('user', 'nutritionist'));
    })->name('admin.users.profile');

    // [New] Update Specialist Info & Add Certificates
    Route::post('/users/update-specialist/{id}', [UserController::class, 'updateSpecialistInfo'])->name('admin.users.update_specialist');
    Route::post('/users/add-certificate/{id}', [UserController::class, 'addCertificate'])->name('admin.users.add_certificate');
    Route::delete('/users/delete-certificate/{id}', [UserController::class, 'deleteCertificate'])->name('admin.users.delete_certificate');

    // --- إدارة الوجبات (Meals Management) ---

    // --- عرض الحميات (Diets - Read Only) ---
    Route::get('/diets', [\App\Http\Controllers\Admin\DietController::class, 'index'])->name('admin.diets.index');
    Route::get('/diets/{id}', [\App\Http\Controllers\Admin\DietController::class, 'show'])->name('admin.diets.show');

    // --- النظام المحاسبي وإدارة السحب (Accounting & Payouts) ---
    Route::get('/accounting', [\App\Http\Controllers\Admin\AccountingController::class, 'index'])->name('admin.accounting.index');
    Route::get('/accounting/payouts', [\App\Http\Controllers\Admin\AccountingController::class, 'payouts'])->name('admin.accounting.payouts');
    Route::post('/accounting/payouts/{id}/approve', [\App\Http\Controllers\Admin\AccountingController::class, 'approvePayout'])->name('admin.accounting.payouts.approve');
    Route::post('/accounting/payouts/{id}/reject', [\App\Http\Controllers\Admin\AccountingController::class, 'rejectPayout'])->name('admin.accounting.payouts.reject');
    Route::post('/accounting/expenses', [\App\Http\Controllers\Admin\AccountingController::class, 'storeExpense'])->name('admin.accounting.expenses.store');
    Route::post('/accounting/penalty', [\App\Http\Controllers\Admin\AccountingController::class, 'applyPenalty'])->name('admin.accounting.penalty.apply');

    // --- معاملات بوابة الدفع BAS (Payment Gateway Monitor) ---
    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentAdminController::class, 'index'])->name('admin.payments.index');
});

// =======================================================
// 2.5 مسارات مشتركة بين Admin و Restaurant Manager
// =======================================================
Route::middleware(['auth', 'role:Admin,Restaurant Manager'])->prefix('admin')->group(function () {

    Route::resource('meals', \App\Http\Controllers\Admin\MealController::class)->names([
        'index' => 'admin.meals.index',
        'create' => 'admin.meals.create',
        'store' => 'admin.meals.store',
        'show' => 'admin.meals.show',
        'edit' => 'admin.meals.edit',
        'update' => 'admin.meals.update',
        'destroy' => 'admin.meals.destroy',
    ]);

    // --- إدارة الطلبات (Orders Management) ---
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

    // --- إدارة المطاعم (Restaurants Management) ---

    Route::post('/restaurants/request-create', [\App\Http\Controllers\Admin\RestaurantController::class, 'requestCreation'])->name('admin.restaurants.request_create');
    Route::resource('restaurants', \App\Http\Controllers\Admin\RestaurantController::class, ['as' => 'admin']);

    // --- إضافة وجبات للمطاعم ---
    Route::post('/restaurants/category', [\App\Http\Controllers\Admin\RestaurantController::class, 'storeCategory'])->name('admin.restaurants.store_category');
    Route::get('/restaurants/{restaurant}/meals/create', [\App\Http\Controllers\Admin\RestaurantController::class, 'createMeal'])->name('admin.restaurants.create_meal');
    Route::post('restaurants/{id}/meals', [\App\Http\Controllers\Admin\RestaurantController::class, 'storeMeal'])->name('admin.restaurants.meals.store');

    // --- إدارة التصنيفات (AJAX) ---
    Route::post('categories', [\App\Http\Controllers\Admin\RestaurantController::class, 'storeCategory'])->name('admin.categories.store');

    // --- AI Suggest Ingredients (AJAX) ---
    Route::post('meals/ai-suggest', [\App\Http\Controllers\Admin\MealController::class, 'aiSuggestIngredients'])->name('admin.meals.ai_suggest');
});

// =======================================================
// 3. مسارات الأخصائي (Specialist Group) ⭐️ محمية بـ Role Middleware ⭐️
// =======================================================
Route::middleware(['auth', 'role:Specialist,Nutrition Manager'])->prefix('specialist')->group(function () {

    // ... (باقي مسارات Specialist تبقى كما هي) ...

    Route::get('/dashboard', [\App\Http\Controllers\Specialist\DashboardController::class, 'index'])->name('specialist.dashboard');

    // --- Profile Routes ---

    // --- إدارة الحميات (Diets Management) ---
    Route::resource('diets', \App\Http\Controllers\Specialist\DietController::class)->names([
        'index' => 'specialist.diets.index',
        'create' => 'specialist.diets.create',
        'store' => 'specialist.diets.store',
        'show' => 'specialist.diets.show',
        'edit' => 'specialist.diets.edit',
        'update' => 'specialist.diets.update',
        'destroy' => 'specialist.diets.destroy',
    ]);

    // --- AI Suggest Meals for Diet (AJAX) ---
    Route::post('diets/ai-suggest-meals', [\App\Http\Controllers\Specialist\DietController::class, 'aiSuggestMeals'])
        ->name('specialist.diets.ai_suggest_meals');

    // --- إدارة الوجبات (Meals for Specialist) ---

    // --- إدارة الوجبات (Meals for Specialist) ---
    Route::get('/dishes', [\App\Http\Controllers\Specialist\MealController::class, 'index'])->name('specialist.dishes');
    Route::get('/meals/pending', [\App\Http\Controllers\Specialist\MealController::class, 'pending'])->name('specialist.meals.pending');
    Route::get('/meals/diets', [\App\Http\Controllers\Specialist\MealController::class, 'getDiets'])->name('specialist.meals.get_diets');
    Route::get('/meals/{id}', [\App\Http\Controllers\Specialist\MealController::class, 'show'])->name('specialist.meals.show');
    Route::put('/meals/{id}', [\App\Http\Controllers\Specialist\MealController::class, 'update'])->name('specialist.meals.update');
    Route::post('/meals/{id}/approve', [\App\Http\Controllers\Specialist\MealController::class, 'approve'])->name('specialist.meals.approve');
    Route::post('/meals/{id}/reject', [\App\Http\Controllers\Specialist\MealController::class, 'reject'])->name('specialist.meals.reject');
    // Moved up to avoid conflict

    Route::post('/meals/{id}/ai-suggest-diets', [\App\Http\Controllers\Specialist\MealController::class, 'aiSuggestDiets'])->name('specialist.meals.ai_suggest_diets');

    Route::get('/users', [\App\Http\Controllers\Specialist\DashboardController::class, 'users'])->name('specialist.users');

    Route::get('/notifications', function () {
        return view('specialist.notifications');
    })->name('specialist.notifications');

    Route::get('/messages', [\App\Http\Controllers\ChatController::class, 'index'])->name('specialist.messages');

    Route::post('/notifications/schedule', [\App\Http\Controllers\NotificationController::class, 'store'])->name('specialist.notifications.schedule');

});

// =======================================================
// 4. مسار توجيه الدور (Role-Based Redirection)
// =======================================================

Route::get('/user-role-redirect', function () {
    if (Auth::check()) {
        $user = Auth::user()->load('roles'); // جلب الأدوار

        if ($user->roles->isEmpty()) {
            return redirect('/');
        }

        $roleName = $user->roles->first()->name;

        if ($roleName === 'Admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($roleName === 'Specialist' || $roleName === 'Nutrition Manager') {
            return redirect()->route('specialist.dashboard');
        } else {
            return redirect('/');
        }
    }

    return redirect()->route('login');
})->middleware(['auth'])->name('user.role.redirect');

// =======================================================
// 5. Chat API Routes
// =======================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index'); // ✅ تم إضافة مسار الاندكس للدردشة
    Route::get('/chat/conversations', [\App\Http\Controllers\ChatController::class, 'getConversations'])->name('chat.conversations');
    Route::get('/chat/messages/{userId}', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');

    // Rate limit sending messages: 30 requests per minute
    Route::middleware(['throttle:30,1'])->group(function () {
        Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    });

    Route::get('/chat/can-send/{userId}', [\App\Http\Controllers\ChatController::class, 'canSendMessage'])->name('chat.can_send');
    Route::get('/chat/user-info/{id}', [\App\Http\Controllers\ChatController::class, 'getUserDetails'])->name('chat.user_info');
});

// =======================================================
// 6. مسارات مدير التغذية (Nutrition Manager Group)
// =======================================================
Route::middleware(['auth', 'role:Nutrition Manager'])->prefix('nutrition-manager')->name('nutrition-manager.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // Reuse admin dashboard or create specific one
    })->name('dashboard');

    Route::resource('ingredients', \App\Http\Controllers\NutritionManager\IngredientController::class);
    Route::resource('chronic-diseases', \App\Http\Controllers\NutritionManager\ChronicDiseaseController::class);
    Route::resource('allergies', \App\Http\Controllers\NutritionManager\AllergyController::class);
    Route::resource('medications', \App\Http\Controllers\NutritionManager\MedicationController::class);
});

Route::get('/list-models', function () {
    $apiKey = config('gemini.api_key');

    // طلب القائمة من جوجل مباشرة لتجاوز مشاكل المكتبة
    $response = Http::get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");

    if ($response->failed()) {
        return 'Error: ' . $response->body();
    }

    $models = $response->json()['models'];

    // تنسيق العرض كجدول HTML بسيط
    echo '<h1>Gemini Available Models</h1>';
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background:#f0f0f0'><th>Model Name (الاسم البرمجي)</th><th>Input Token Limit (للمدخلات)</th><th>Output Token Limit (للرد)</th></tr>";

    foreach ($models as $model) {
        // عرض فقط موديلات gemini
        if (str_contains($model['name'], 'gemini')) {
            echo '<tr>';
            echo '<td>' . str_replace('models/', '', $model['name']) . '</td>';
            echo '<td>' . number_format($model['inputTokenLimit']) . '</td>';
            echo '<td>' . number_format($model['outputTokenLimit']) . '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
});
