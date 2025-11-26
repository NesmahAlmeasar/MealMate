<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminController; 
use App\Http\Controllers\Specialist\SpecialistController; 
use Illuminate\Support\Facades\Auth; // لـ Route::get('/user-role-redirect')

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

require __DIR__.'/auth.php';

// =======================================================
// 2. مسارات لوحة تحكم المدير (Admin Group) ⭐️ تم تنظيفها ⭐️
// =======================================================
Route::middleware(['auth'])->prefix('admin')->group(function () {
    
    // لوحة التحكم الرئيسية
    Route::get('/dashboard', function () {
        return view('admin.dashboard-2');
    })->name('admin.dashboard');

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

    // بروفايل المستخدم
    Route::get('/users/profile/{id}', function ($id) {
        return view('admin.client_profile');
    })->name('admin.users.profile');
    
    // مسارات أخرى خاصة بالمدير
    Route::get('/messages', function () {
        return view('admin.messages');
    })->name('admin.messages');

    // --- إدارة الوجبات (Meals Management) ---
    Route::resource('meals', \App\Http\Controllers\Admin\MealController::class)->names([
        'index' => 'admin.meals.index',
        'create' => 'admin.meals.create',
        'store' => 'admin.meals.store',
        'show' => 'admin.meals.show',
        'edit' => 'admin.meals.edit',
        'update' => 'admin.meals.update',
        'destroy' => 'admin.meals.destroy',
    ]);

    // --- عرض الحميات (Diets - Read Only) ---
    Route::get('/diets', [\App\Http\Controllers\Admin\DietController::class, 'index'])->name('admin.diets.index');
    Route::get('/diets/{id}', [\App\Http\Controllers\Admin\DietController::class, 'show'])->name('admin.diets.show');

    // --- إدارة الطلبات (Orders Management) ---
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
});


// =======================================================
// 3. مسارات الأخصائي (Specialist Group)
// =======================================================
Route::middleware(['auth'])->prefix('specialist')->group(function () {
    
    // ... (باقي مسارات Specialist تبقى كما هي) ...

    Route::get('/dashboard', function () {
        return view('specialist.dashboard-2');
    })->name('specialist.dashboard');

    // --- Profile Routes ---
    Route::get('/profile', [\App\Http\Controllers\Specialist\ProfileController::class, 'show'])->name('specialist.profile.show');
    Route::post('/profile', [\App\Http\Controllers\Specialist\ProfileController::class, 'update'])->name('specialist.profile.update');
    
    
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
    
    // --- إدارة الوجبات (Meals for Specialist) ---
    
    // --- إدارة الوجبات (Meals for Specialist) ---
    Route::get('/dishes', [\App\Http\Controllers\Specialist\MealController::class, 'index'])->name('specialist.dishes');
    Route::get('/meals/pending', [\App\Http\Controllers\Specialist\MealController::class, 'pending'])->name('specialist.meals.pending');
    Route::get('/meals/{id}', [\App\Http\Controllers\Specialist\MealController::class, 'show'])->name('specialist.meals.show');
    Route::post('/meals/{id}/approve', [\App\Http\Controllers\Specialist\MealController::class, 'approve'])->name('specialist.meals.approve');
    Route::post('/meals/{id}/reject', [\App\Http\Controllers\Specialist\MealController::class, 'reject'])->name('specialist.meals.reject');

    Route::get('/users', function () {
        return view('specialist.users');
    })->name('specialist.users');

    Route::get('/notifications', function () {
        return view('specialist.notifications');
    })->name('specialist.notifications');

    Route::get('/messages', function () {
        return view('specialist.messages');
    })->name('specialist.messages');

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
        } elseif ($roleName === 'Specialist') {
            return redirect()->route('specialist.dashboard');
        } else {
            return redirect('/'); 
        }
    }
    return redirect()->route('login');
})->middleware(['auth'])->name('user.role.redirect');