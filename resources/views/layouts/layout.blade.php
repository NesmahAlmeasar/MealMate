<!DOCTYPE html>
{{-- Unified Layout for Admin and Specialist --}}
<html lang="ar" dir="rtl"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>@yield('title', 'MealMate')</title>

    {{-- General CSS --}}
    <link rel="stylesheet" href="{{ asset('css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('css/diets-styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    {{-- Arabic Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

    <style>
        body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, textarea, select, label {
            font-family: "Cairo", sans-serif !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    
    <div class="container">
        {{-- Sidebar --}}
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon">🍽</div>
                <div class="logo-text" data-i18n="logo">MealMate</div>
            </div>

            <nav class="nav-menu">
                
                {{-- Admin Links --}}
                @if(Auth::check() && Auth::user()->hasRole('Admin'))
                    
                    <div class="nav-section-label" style="padding: 10px 20px; color: #888; font-size: 12px; font-weight: bold; text-transform: uppercase;">المدير</div>

                    <a href="{{ route('admin.dashboard') }}" 
                       class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span data-i18n="dashboard">لوحة التحكم</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                        <span class="nav-icon">🧑‍🧑‍🧒‍🧒</span>
                        <span data-i18n="users">المستخدمين</span>
                    </a>

                    <a href="{{ route('admin.meals.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.meals.*') ? 'active' : '' }}">
                        <span class="nav-icon">🍽️</span>
                        <span data-i18n="meals">الوجبات</span>
                    </a>

                    <a href="{{ route('admin.restaurants.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.restaurants.*') ? 'active' : '' }}">
                        <span class="nav-icon">🏪</span>
                        <span data-i18n="restaurants">المطاعم</span>
                    </a>

                    <a href="{{ route('admin.diets.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.diets.*') ? 'active' : '' }}">
                        <span class="nav-icon">📋</span>
                        <span data-i18n="diets">مكتبة الحميات</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.orders.*') ? 'active' : '' }}">
                        <span class="nav-icon">🛒</span>
                        <span data-i18n="orders">الطلبات</span>
                    </a>

                    <a href="{{ route('admin.messages') }}" 
                       class="nav-item {{ Request::routeIs('admin.messages') ? 'active' : '' }}">
                        <span class="nav-icon">💬</span>
                        <span data-i18n="messages">الرسائل</span>
                    </a>

                @endif

                {{-- Specialist Links --}}
                @if(Auth::check() && Auth::user()->hasRole('Specialist'))
                    
                    @if(Auth::user()->hasRole('Admin'))
                        <div class="nav-divider" style="height: 1px; background: #eee; margin: 10px 20px;"></div>
                    @endif

                    <div class="nav-section-label" style="padding: 10px 20px; color: #888; font-size: 12px; font-weight: bold; text-transform: uppercase;">الأخصائي</div>

                    <a href="{{ route('specialist.dashboard') }}" 
                       class="nav-item {{ Request::routeIs('specialist.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span data-i18n="dashboard">لوحة التحكم</span>
                    </a>
                    
                    <a href="{{ route('specialist.diets.index') }}" 
                       class="nav-item {{ Request::routeIs('specialist.diets.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-apple-alt"></i></span>
                        <span data-i18n="diets">حمياتي</span>
                    </a>
                    
                    <a href="{{ url('specialist/dishes') }}" 
                       class="nav-item {{ Request::is('specialist/dishes*') ? 'active' : '' }}">
                        <span class="nav-icon">🥗</span>
                        <span data-i18n="dishes">الأطباق</span>
                    </a>

                    <a href="{{ route('specialist.meals.pending') }}" 
                       class="nav-item {{ Request::routeIs('specialist.meals.pending') ? 'active' : '' }}">
                        <span class="nav-icon">⏳</span>
                        <span data-i18n="pending_meals">وجبات معلقة</span>
                    </a>
                    
                    <a href="{{ url('specialist/users') }}" 
                       class="nav-item {{ Request::is('specialist/users*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        <span data-i18n="users">عملائي</span>
                    </a>

                    <a href="{{ route('shared.profile') }}" 
                       class="nav-item {{ Request::routeIs('shared.profile') ? 'active' : '' }}">
                        <span class="nav-icon">👤</span>
                        <span data-i18n="profile">الملف الشخصي</span>
                    </a>

                @endif

            </nav>

            <div class="doctor-card">
                <div class="doctor-card-image">
                    {{ (Auth::check() && Auth::user()->hasRole('Admin')) ? '👨‍💼' : '👨‍⚕️' }}
                </div>
                <div class="doctor-card-text">
                    @if(Auth::check() && Auth::user()->hasRole('Admin'))
                        أهلاً المدير
                    @else
                        أهلاً الأخصائي
                    @endif
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="main-content">
            <header class="header">
                <div class="search-bar">
                    <input type="text" placeholder="Search..." data-i18n-placeholder="searchPlaceholder">
                    <span class="search-icon">🔍</span>
                </div>

          
                
                    {{-- User Profile --}}
                    <div class="user-profile">
                        <div class="user-avatar">
                            {{ Auth::check() ? strtoupper(substr(Auth::user()->Fname ?? 'U', 0, 1)) : 'G' }}
                        </div>
                        <div class="user-name">
                            {{ Auth::check() ? (Auth::user()->Fname . ' ' . Auth::user()->Lname) : 'Guest' }}
                        </div>
                    </div>
                </div>
            </header>

            @yield('content')

        </main>
    </div>

    <div id="toastMessage" class="toast-message"></div>

    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
