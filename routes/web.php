<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/specialist/dishes', function () {
    // 'specialist.dishes' تعني:
    // اذهب إلى مجلد resources/views/specialist/
    // واعرض ملف dishes.blade.php
    return view('specialist.dishes');
}); 

// هذا هو الرابط لصفحة "إضافة حمية"
Route::get('/specialist/diets/add', function () {
    // 'specialist.diet-add-new' تعني:
    // اذهب إلى مجلد resources/views/specialist/
    // واعرض ملف diet-add-new.blade.php
    return view('specialist.diet-add-new');
});

// يمكنك إضافة رابط افتتاحي بسيط للوحة التحكم
Route::get('/specialist/dashboard', function () {
    // تأكد من أن ملف 'dashboard-2.blade.php' موجود في مجلد 'specialist'
    return view('specialist.dashboard-2');
});
// --- مسارات إدارة العملاء (للأخصائي) ---

// المسار لصفحة "إضافة عميل"
Route::get('/specialist/users/add', function () {
    // يعرض: resources/views/specialist/add_user.blade.php
    return view('specialist.add_user');
});

// المسار لصفحة "بروفايل العميل"
// {id} هو متغير ديناميكي، يمكنك وضع أي رقم مكانه في الرابط
Route::get('/specialist/users/profile/{id}', function ($id) {
    // يعرض: resources/views/specialist/client_profile.blade.php
    // لاحقاً، يمكننا استخدام $id لجلب بيانات العميل من قاعدة البيانات
    return view('specialist.client_profile');
});

// (سنحتاج أيضاً لصفحة القائمة الرئيسية، لكن دعنا نختبر هاتين أولاً)
Route::get('/specialist/users', function () {
    // هذا المسار سيعرض 'users.blade.php' (الذي لم نجهزه بعد)
    // حالياً سيعيد توجيهك لصفحة إضافة مستخدم
    return redirect('/specialist/users/add');
});
// --- مسارات لوحة التحكم وتفاصيل الحمية ---

// المسار لصفحة لوحة التحكم (الرئيسية للأخصائي)
Route::get('/specialist/dashboard', function () {
    // يعرض: resources/views/specialist/dashboard-2.blade.php
    return view('specialist.dashboard-2');
});

// المسار لصفحة "تفاصيل الحمية"
// {id} هو متغير ديناميكي
Route::get('/specialist/diets/details/{id}', function ($id) {
    // يعرض: resources/views/specialist/diet-details-new.blade.php
    return view('specialist.diet-details-new');
});

// --- مسارات الحميات والبروفايل ---

// المسار لصفحة "الحميات" الرئيسية
Route::get('/specialist/diets', function () {
    // يعرض: resources/views/specialist/diets-main-new.blade.php
    return view('specialist.diets-main-new');
});

// المسار لصفحة "بروفايل الأخصائي"
Route::get('/specialist/profile', function () {
    // يعرض: resources/views/specialist/doctor-1.blade.php
    return view('specialist.doctor-1');
});
// --- مسارات تعديل العميل والرسائل ---

// المسار لصفحة "تعديل عميل"
// {id} هو متغير ديناميكي ليطابق العميل
Route::get('/specialist/users/edit/{id}', function ($id) {
    // يعرض: resources/views/specialist/edit_user.blade.php
    // لاحقاً سنستخدم $id لجلب بيانات العميل الصحيح
    return view('specialist.edit_user');
});

// المسار لصفحة "الرسائل"
Route::get('/specialist/messages', function () {
    // يعرض: resources/views/specialist/messages.blade.php
    return view('specialist.messages');
});

// --- مسارات العملاء، الوجبات المؤخراً، والإشعارات ---

// (تحديث للمسار القديم)
// المسار لصفحة "العملاء" الرئيسية (الجدول)
Route::get('/specialist/users', function () {
    // يعرض: resources/views/specialist/users.blade.php
    return view('specialist.users');
});

// المسار لصفحة "الوجبات المضافة مؤخراً"
Route::get('/specialist/recent-meals', function () {
    // يعرض: resources/views/specialist/recent_meals.blade.php
    return view('specialist.recent_meals');
});

// المسار لصفحة "الإشعارات"
Route::get('/specialist/notifications', function () {
    // يعرض: resources/views/specialist/notifications.blade.php
    return view('specialist.notifications');
});