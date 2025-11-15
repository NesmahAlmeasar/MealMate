<!DOCTYPE html>
{{-- تم تثبيت الاتجاه LTR بناءً على ملفات JS السابقة --}}
<html lang="en" dir="ltr"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- 1. عنوان صفحة ديناميكي --}}
    <title>@yield('title', 'MealMate') - لوحة تحكم الأخصائي</title>

    {{-- 2. إصلاح مسارات CSS (استخدام asset()) --}}
    <link rel="stylesheet" href="{{ asset('css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('css/diets-styles.css') }}"> {{-- هذا قد يكون عاماً أيضاً --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    {{-- 3. مكان للـ CSS الخاص بالصفحات الفرعية --}}
    @stack('styles')
</head>
<body>
    
    <div class="container">
        {{-- =============================================== --}}
        {{-- 4. الكود المشترك: القائمة الجانبية (Sidebar) --}}
        {{-- =============================================== --}}
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon">🍽</div>
                <div class="logo-text" data-i18n="logo">MealMate</div>
            </div>

            {{-- ======================================================== --}}
            {{-- ⭐️ التعديل هنا: إضافة كلاس active تلقائياً ⭐️ --}}
            {{-- ======================================================== --}}
            <nav class="nav-menu">
                
                {{-- 
                    نستخدم Request::is() للتحقق من الرابط
                    'specialist/dashboard' -> تطابق تام
                    'specialist/diets*' -> تطابق أي شيء يبدأ بـ 'specialist/diets' (مثل /diets, /diets/add, /diets/details/1)
                --}}

                <a href="{{ url('specialist/dashboard') }}" 
                   class="nav-item {{ Request::is('specialist/dashboard') ? 'active' : '' }}" 
                   data-page="dashboard">
                    <span class="nav-icon">📊</span>
                    <span data-i18n="dashboard">Dashboard</span>
                </a>
                
                <a href="{{ url('specialist/diets') }}" 
                   class="nav-item {{ Request::is('specialist/diets*') ? 'active' : '' }}" 
                   data-page="diets">
                    <span class="nav-icon"><i class="fas fa-apple-alt"></i></span>
                    <span data-i18n="diets">Diets</span>
                </a>
                
                <a href="{{ url('specialist/dishes') }}" 
                   class="nav-item {{ Request::is('specialist/dishes*') || Request::is('specialist/recent-meals*') ? 'active' : '' }}" 
                   data-page="dishes">
                    <span class="nav-icon">🥗</span>
                    <span data-i18n="dishes">Dishes</span>
                </a>
                
                <a href="{{ url('specialist/users') }}" 
                   class="nav-item {{ Request::is('specialist/users*') ? 'active' : '' }}" 
                   data-page="users"> {{-- تم تعديل data-page ليتطابق --}}
                    <span class="nav-icon">👥</span>
                    <span data-i18n="users">Users</span>
                </a>
            </nav>
            {{-- ======================================================== --}}
            {{-- ⭐️ نهاية التعديل ⭐️ --}}
            {{-- ======================================================== --}}


            <div class="doctor-card">
                <div class="doctor-card-image">👨‍⚕️</div>
                <div class="doctor-card-text" data-i18n="youAreHere">
                    You are here to change our lives for the better
                </div>
            </div>
        </aside>

        {{-- =============================================== --}}
        {{-- 6. الكود المشترك: المحتوى الرئيسي والرأس (Header) --}}
        {{-- =============================================== --}}
        <main class="main-content">
            <header class="header">
                <div class="search-bar">
                    <input type="text" placeholder="Search here..." data-i18n-placeholder="searchPlaceholder">
                    <span class="search-icon">🔍</span>
                </div>

                <div class="header-actions">
                    <div class="language-toggle">
                        <button class="lang-btn" data-lang="en">English</button>
                        <button class="lang-btn" data-lang="ar">العربية</button>
                    </div>

                    {{-- 7. إصلاح روابط الأيقونات (Header) --}}
                  
                    <button class="icon-button" title="Notifications" onclick="window.location.href='{{ url('specialist/notifications') }}';">
                        🔔
                        <span class="notification-badge">2</span>
                    </button>
                    <button class="icon-button" title="Messages" onclick="window.location.href='{{ url('specialist/messages') }}';">
                        💬
                        <span class="notification-badge">1</span>
                    </button>

                    <div class="user-profile" onclick="window.location.href='{{ url('specialist/profile') }}';">
                        <div class="user-avatar">S</div>
                        <div class="user-name" data-i18n="hiSamantha">Hi, Samantha</div>
                    </div>
                </div>
            </header>

            {{-- =============================================== --}}
            {{-- 8. (الأهم) مكان المحتوى المتغير --}}
            {{-- =============================================== --}}
            @yield('content')

        </main>
    </div>

    {{-- 9. إضافة التوست (Toast) بشكل عام ليكون متاحاً للجميع --}}
    <div id="toastMessage" class="toast-message"></div>

    {{-- 10. إصلاح مسار JS (ملفك الموحّد) --}}
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- 11. مكان للـ JS الخاص بالصفحات الفرعية --}}
    @stack('scripts')
</body>
</html>