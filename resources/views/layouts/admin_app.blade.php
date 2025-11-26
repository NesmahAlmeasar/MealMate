<!DOCTYPE html>
{{-- تم تثبيت الاتجاه LTR --}}
<html lang="en" dir="ltr"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- 1. عنوان صفحة ديناميكي --}}
    <title>@yield('title', 'MealMate')</title>

    {{-- 2. ملفات CSS العامة --}}
    <link rel="stylesheet" href="{{ asset('css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('css/diets-styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    {{-- 3. مكان للـ CSS الخاص بالصفحات الفرعية --}}
    @stack('styles')
</head>
<body>
    
    <div class="container">
        {{-- =============================================== --}}
        {{-- 4. القائمة الجانبية (Sidebar) الموحدة --}}
        {{-- =============================================== --}}
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon">🍽</div>
                <div class="logo-text" data-i18n="logo">MealMate</div>
            </div>

            <nav class="nav-menu">
                
                {{-- ============================================ --}}
                {{-- 🅰️ روابط المدير (Admin Links) --}}
                {{-- ============================================ --}}
                @if(Auth::check() && Auth::user()->hasRole('Admin'))
                    
                    <a href="{{ route('admin.dashboard') }}" 
                       class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span data-i18n="dashboard">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                        <span class="nav-icon">🧑‍🧑‍🧒‍🧒</span>
                        <span data-i18n="users">Users Management</span>
                    </a>

                    <a href="{{ route('admin.meals.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.meals.*') ? 'active' : '' }}">
                        <span class="nav-icon">🍽️</span>
                        <span data-i18n="meals">Meals Management</span>
                    </a>

                    <a href="{{ route('admin.diets.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.diets.*') ? 'active' : '' }}">
                        <span class="nav-icon">📋</span>
                        <span data-i18n="diets">Diets Library</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.orders.*') ? 'active' : '' }}">
                        <span class="nav-icon">🛒</span>
                        <span data-i18n="orders">Orders</span>
                    </a>

                    <a href="{{ route('admin.messages') }}" 
                       class="nav-item {{ Request::routeIs('admin.messages') ? 'active' : '' }}">
                        <span class="nav-icon">💬</span>
                        <span data-i18n="messages">Messages</span>
                    </a>

                @endif

                {{-- ============================================ --}}
                {{-- 🅱️ روابط الأخصائي (Specialist Links) --}}
                {{-- ============================================ --}}
                @if(Auth::check() && Auth::user()->hasRole('Specialist'))
                    
                    <a href="{{ route('specialist.dashboard') }}" 
                       class="nav-item {{ Request::routeIs('specialist.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span data-i18n="dashboard">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('specialist.diets.index') }}" 
                       class="nav-item {{ Request::routeIs('specialist.diets.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-apple-alt"></i></span>
                        <span data-i18n="diets">Diets</span>
                    </a>
                    
                    {{-- روابط إضافية للأخصائي (تأكد من وجود المسارات في web.php) --}}
                    <a href="{{ url('specialist/dishes') }}" 
                       class="nav-item {{ Request::is('specialist/dishes*') ? 'active' : '' }}">
                        <span class="nav-icon">🥗</span>
                        <span data-i18n="dishes">Dishes</span>
                    </a>
                    
                    <a href="{{ url('specialist/users') }}" 
                       class="nav-item {{ Request::is('specialist/users*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        <span data-i18n="users">My Clients</span>
                    </a>

                    <a href="{{ route('specialist.profile.show') }}" 
                       class="nav-item {{ Request::routeIs('specialist.profile.*') ? 'active' : '' }}">
                        <span class="nav-icon">👤</span>
                        <span data-i18n="profile">Profile</span>
                    </a>

                @endif

            </nav>

            <div class="doctor-card">
                <div class="doctor-card-image">
                    {{ (Auth::check() && Auth::user()->hasRole('Admin')) ? '👨‍💼' : '👨‍⚕️' }}
                </div>
                <div class="doctor-card-text">
                    @if(Auth::check() && Auth::user()->hasRole('Admin'))
                        Welcome Admin, you are in control.
                    @else
                        You are here to change lives for the better.
                    @endif
                </div>
            </div>
        </aside>

        {{-- =============================================== --}}
        {{-- 6. المحتوى الرئيسي والرأس (Header) --}}
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

                    {{-- زر تسجيل الخروج --}}
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="icon-button" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>

                    {{-- بروفايل المستخدم --}}
                    <div class="user-profile">
                        <div class="user-avatar">
                            {{ Auth::check() ? strtoupper(substr(Auth::user()->Fname ?? 'U', 0, 1)) : 'G' }}
                        </div>
                        <div class="user-name">
                            {{-- عرض اسم المستخدم ديناميكياً --}}
                            {{ Auth::check() ? (Auth::user()->Fname . ' ' . Auth::user()->Lname) : 'Guest' }}
                        </div>
                    </div>
                </div>
            </header>

            {{-- =============================================== --}}
            {{-- 8. مكان المحتوى المتغير --}}
            {{-- =============================================== --}}
            @yield('content')

        </main>
    </div>

    {{-- 9. التوست (Toast) --}}
    <div id="toastMessage" class="toast-message"></div>

    {{-- 10. ملف JS الموحد --}}
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- 11. مكان للـ JS الخاص بالصفحات الفرعية --}}
    @stack('scripts')
</body>
</html>