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
    <link rel="stylesheet" href="{{ asset('css/unified-design.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    {{-- Arabic Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

    <style>
        body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, textarea, select, label {
            font-family: "Cairo", sans-serif !important;
        }

        /* Sidebar Toggle & Responsive Styles */
        /* Sidebar Toggle & Responsive Styles */
        .sidebar {
            position: fixed;
            right: 0; /* RTL Default */
            top: 0;
            height: 100vh;
            width: 280px;
            background: white;
            box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1); /* Shadow to left */
            transition: transform 300ms ease-in-out;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            transform: translateX(100%); /* Move to right to hide */
        }

        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem; /* RTL Default */
            z-index: 1001;
            background: linear-gradient(135deg, var(--olive-medium), var(--olive-dark));
            color: white;
            border: none;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 300ms ease;
        }

        .sidebar-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 300ms ease-in-out;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        .main-content {
            margin-right: 280px; /* RTL Default */
            margin-left: 0;
            transition: margin-right 300ms ease-in-out;
        }

        /* Logo Toggle Functionality */
        .logo {
            cursor: pointer;
            user-select: none;
        }

        /* Responsive Behavior */
        @media (max-width: 1024px) {
            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar {
                transform: translateX(100%); /* Hidden to Right */
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 0;
            }
        }

        @media (min-width: 1025px) {
            .sidebar {
                transform: translateX(0) !important;
            }
        }

        /* Unified Action Buttons */
        .diet-card-actions {
            display: flex;
            gap: 8px;
        }

        .diet-action-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background-color: var(--olive-very-light);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            text-decoration: none !important;
            transition: all 0.3s ease;
            color: inherit;
        }

        .diet-action-btn:hover {
            background-color: var(--olive-light);
            transform: scale(1.1);
        }

        .diet-action-btn.edit:hover {
            background-color: #DBEAFE;
            color: var(--blue-primary);
        }

        .diet-action-btn.delete:hover {
            background-color: #FEE2E2;
            color: var(--red-accent);
        }

        /* Toast Notification Styles */
        .toast-message {
            position: fixed;
            bottom: 30px;
            left: 30px; 
            background: white;
            color: var(--text-dark);
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            max-width: 400px;
            z-index: 9999;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-right: 5px solid var(--olive-medium);
        }
        
        .toast-message.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>

    <script>
        // Global Fallback Image Script
        // This runs immediately to catch images as they load
        document.addEventListener('error', function(e) {
            if (e.target.tagName.toLowerCase() === 'img') {
                // Prevent infinite loop if fallback also fails
                if (e.target.src.includes('mealmate.png')) return;
                
                e.target.src = "{{ asset('images/mealmate.png') }}";
                e.target.alt = "Image not found";
            }
        }, true); // Capture phase to catch error events
    </script>

    @stack('styles')
</head>
<body>
    {{-- Sidebar Toggle Button --}}
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
        🍽️
    </button>

    {{-- Sidebar Overlay for Mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="container">
        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <div class="logo-icon">🍽</div>
                <div class="logo-text" data-i18n="logo">MealMate</div>
            </div>

            <nav class="nav-menu">
                           {{-- Shared Links (Visible to Admin, Specialist, Nutrition Manager) --}}
                <div class="nav-section-label" style="padding: 10px 20px; color: #6B8E23; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">🌐 الصفحات المشتركة</div>
                
                <a href="{{ route('shared.dashboard') }}" 
                   class="nav-item {{ Request::routeIs('shared.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">🏠</span>
                    <span data-i18n="sharedDashboard">لوحة التحكم الشاملة</span>
                </a>
                @if(Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Specialist') || Auth::user()->hasRole('Nutrition Manager')))
                    <a href="{{ route('admin.messages') }}" 
                       class="nav-item {{ Request::routeIs('admin.messages') ? 'active' : '' }}">
                        <span class="nav-icon">💬</span>
                        <span data-i18n="messages">الرسائل والمحادثات</span>
                    </a>
              
                    <a href="{{ route('shared.diets') }}" 
                       class="nav-item {{ Request::routeIs('shared.diets*') ? 'active' : '' }}">
                        <span class="nav-icon">📋</span>
                        <span data-i18n="dietsLibrary">مكتبة الحميات الغذائية</span>
                    </a>
                    
                    <a href="{{ route('shared.dishes') }}" 
                       class="nav-item {{ Request::routeIs('shared.dishes*') ? 'active' : '' }}">
                        <span class="nav-icon">🍽️</span>
                        <span data-i18n="dishesLibrary">مكتبة الأطباق والوجبات</span>
                    </a>
                @endif

                {{-- Admin Links --}}
                @if(Auth::check() && Auth::user()->hasRole('Admin'))
                    <div class="nav-divider" style="height: 1px; background: #eee; margin: 10px 20px;"></div>
                    <div class="nav-section-label" style="padding: 10px 20px; color: #556B2F; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">👨‍💼 الإدارة العامة</div>

                    <a href="{{ route('admin.users.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                        <span class="nav-icon">🧑‍🧑‍🧒‍🧒</span>
                        <span data-i18n="users">إدارة المستخدمين</span>
                    </a>

                    <a href="{{ route('admin.meals.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.meals.*') ? 'active' : '' }}">
                        <span class="nav-icon">🍽️</span>
                        <span data-i18n="meals">إدارة الأطباق</span>
                    </a>

                    <a href="{{ route('admin.restaurants.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.restaurants.*') ? 'active' : '' }}">
                        <span class="nav-icon">🏪</span>
                        <span data-i18n="restaurants">المطاعم والعمولات</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.orders.*') ? 'active' : '' }}">
                        <span class="nav-icon">🛒</span>
                        <span data-i18n="orders">إدارة الطلبات</span>
                    </a>

                    <a href="{{ route('consultation-types.index') }}" 
                       class="nav-item {{ Request::routeIs('consultation-types.*') ? 'active' : '' }}">
                        <span class="nav-icon">🩺</span>
                        <span data-i18n="consultationTypes">أنواع الاستشارات</span>
                    </a>

                    {{-- Financial & Accounting Section --}}
                    <div class="nav-divider" style="height: 1px; background: #eee; margin: 10px 20px;"></div>
                    <div class="nav-section-label" style="padding: 10px 20px; color: #1565C0; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">💰 الإدارة المالية والمحاسبة</div>

                    <a href="{{ route('admin.accounting.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.accounting.index') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span>الدفتر المحاسبي والقيود</span>
                    </a>

                    <a href="{{ route('admin.accounting.payouts') }}" 
                       class="nav-item {{ Request::routeIs('admin.accounting.payouts') ? 'active' : '' }}">
                        <span class="nav-icon">💳</span>
                        <span>طلبات سحب الأرباح</span>
                    </a>

                    <a href="{{ route('admin.payments.index') }}" 
                       class="nav-item {{ Request::routeIs('admin.payments.*') ? 'active' : '' }}">
                        <span class="nav-icon">🛒</span>
                        <span>معاملات بوابة بس الإلكترونية</span>
                    </a>
                @endif

                {{-- Specialist Links --}}
                @if(Auth::check() && (Auth::user()->hasRole('Specialist') || Auth::user()->hasRole('Nutrition Manager')))
                    @if(Auth::user()->hasRole('Admin'))
                        <div class="nav-divider" style="height: 1px; background: #eee; margin: 10px 20px;"></div>
                    @endif

                    <div class="nav-section-label" style="padding: 10px 20px; color: #888; font-size: 12px; font-weight: bold; text-transform: uppercase;">👨‍⚕️ الأخصائي</div>

                    <a href="{{ url('specialist/users') }}" 
                       class="nav-item {{ Request::is('specialist/users*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        <span data-i18n="users">الاستشارات والعملاء</span>
                    </a>
                @endif

                {{-- Nutrition Manager Links --}}
                @if(Auth::check() && Auth::user()->hasRole('Nutrition Manager'))
                    @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Specialist'))
                        <div class="nav-divider" style="height: 1px; background: #eee; margin: 10px 20px;"></div>
                    @endif

                    <div class="nav-section-label" style="padding: 10px 20px; color: #888; font-size: 12px; font-weight: bold; text-transform: uppercase;">🥗 مدير التغذية</div>

                    <a href="{{ route('nutrition-manager.ingredients.index') }}" 
                       class="nav-item {{ Request::routeIs('nutrition-manager.ingredients.*') ? 'active' : '' }}">
                        <span class="nav-icon">🥕</span>
                        <span data-i18n="ingredients">المكونات الغذائية</span>
                    </a>
                    
                    <a href="{{ route('nutrition-manager.chronic-diseases.index') }}" 
                       class="nav-item {{ Request::routeIs('nutrition-manager.chronic-diseases.*') ? 'active' : '' }}">
                        <span class="nav-icon">🩺</span>
                        <span data-i18n="diseases">الأمراض المزمنة</span>
                    </a>

                    <a href="{{ route('nutrition-manager.allergies.index') }}" 
                       class="nav-item {{ Request::routeIs('nutrition-manager.allergies.*') ? 'active' : '' }}">
                        <span class="nav-icon">⚠️</span>
                        <span data-i18n="allergies">الحساسية</span>
                    </a>
                    
                    <a href="{{ route('nutrition-manager.medications.index') }}" 
                       class="nav-item {{ Request::routeIs('nutrition-manager.medications.*') ? 'active' : '' }}">
                        <span class="nav-icon">💊</span>
                        <span data-i18n="medications">الأدوية</span>
                    </a>

                    <a href="{{ route('specialist.meals.pending') }}" 
                       class="nav-item {{ Request::routeIs('specialist.meals.pending') ? 'active' : '' }}">
                        <span class="nav-icon">⏳</span>
                        <span data-i18n="pending_meals">وجبات معلقة</span>
                    </a>
                @endif

                @if(Auth::check() && Auth::user()->hasRole('Restaurant Manager'))
                    <div class="nav-divider" style="height: 1px; background: #eee; margin: 10px 20px;"></div>
                    <div class="nav-section-label" style="padding: 10px 20px; color: #888; font-size: 12px; font-weight: bold; text-transform: uppercase;">🍽️ مدير المطعم</div>

                    {{-- Restaurant Profile --}}
                    <a href="{{ route('admin.restaurants.index', ['scope' => 'my_restaurant']) }}" 
                       class="nav-item {{ Request::routeIs('admin.restaurants.*') ? 'active' : '' }}">
                        <span class="nav-icon">🏪</span>
                        <span data-i18n="restaurants">مطعمي</span>
                    </a>

                    {{-- Orders --}}
                    <a href="{{ route('admin.orders.index', ['scope' => 'my_restaurant']) }}" 
                       class="nav-item {{ Request::routeIs('admin.orders.*') ? 'active' : '' }}">
                        <span class="nav-icon">🛒</span>
                        <span data-i18n="orders">طلبات مطعمي</span>
                    </a>
                @endifaurant']) }}" 
                       class="nav-item {{ Request::routeIs('admin.orders.*') ? 'active' : '' }}">
                        <span class="nav-icon">🛒</span>
                        <span data-i18n="orders">طلبات مطعمي </span>
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
              
                {{-- 1. Right Side: User Profile --}}
                <a href="{{ route('shared.profile') }}" class="nav-item {{ Request::routeIs('profile.*') ? 'active' : '' }}" style="text-decoration: none;">
                    <div class="user-profile">
                        <div class="user-avatar" style="overflow: hidden; padding: 0;">
                            @if(Auth::check() && Auth::user()->photo_url)
                                <img src="{{ filter_var(Auth::user()->photo_url, FILTER_VALIDATE_URL) ? Auth::user()->photo_url : asset('storage/' . Auth::user()->photo_url) }}" 
                                     alt="Profile" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.onerror=null;this.src='{{ asset('images/profile.png') }}';">
                            @else
                                <img src="{{ asset('images/profile.png') }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                            @endif
                        </div>
                        <div style="font-weight: bold; color: var(--text-dark);">
                            {{ Auth::check() ? (Auth::user()->Fname . ' ' . Auth::user()->Lname) : 'Guest' }}
                        </div>
                    </div>
                </a>

                {{-- 2. Left Side: Actions (Notification + Logout) --}}
                <div class="header-actions" style="margin-right: auto; display: flex; align-items: center; gap: 15px;">
                    
                    {{-- Notification Bell --}}
                    <div class="notification-wrapper" style="position: relative;">
                        <a href="{{ url('notifications') }}" class="icon-button" style="text-decoration: none; position: relative; font-size: 20px;">
                            🔔
                            @php
                                $unreadCount = Auth::check() ? Auth::user()->notifications()->unread()->count() : 0;
                            @endphp
                            @if($unreadCount > 0)
                                <span id="nav-notification-badge" class="notification-badge" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center;">
                                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                    
                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="icon-button" title="Logout">
                            🚪
                        </button>
                    </form>
                </div>

                 

            </header>

            @yield('content')

        </main>
    </div>

    <div id="toastMessage" class="toast-message"></div>

    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle Functionality
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const logo = document.querySelector('.logo');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
            }

            function closeSidebar() {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            // Toggle button click
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            // Logo click (works on all screen sizes)
            if (logo) {
                logo.addEventListener('click', function() {
                    // Only toggle on tablets and mobile
                    if (window.innerWidth <= 1024) {
                        toggleSidebar();
                    }
                });
            }

            // Overlay click to close
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar on window resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1024) {
                    closeSidebar();
                }
            });

            // Check notifications count periodically
            // ==========================================
            //       SMART NOTIFICATION SYSTEM
            // ==========================================
            let lastNotificationId = null;
            let isFirstLoad = true;
            const toastElement = document.getElementById('toastMessage');

            function showToast(title, message, type = 'info') {
                if (!toastElement) return;

                // Set content
                toastElement.innerHTML = `
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <div style="font-size: 20px;">${type === 'success' ? '✅' : '🔔'}</div>
                        <div>
                            <div style="font-weight: bold; margin-bottom: 4px;">${title}</div>
                            <div style="font-size: 13px; opacity: 0.9;">${message}</div>
                        </div>
                    </div>
                `;
                
                // Show
                toastElement.classList.add('show');
                
                // Auto hide after 4 seconds
                setTimeout(() => {
                    toastElement.classList.remove('show');
                }, 4000);
            }

            function updateNotificationBadge() {
                // Use the new Web Route instead of API route to share session cookies reliably
                fetch('/notifications/check', {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    if(data.status === 'success') {
                        // 1. Update Badge
                        const badge = document.getElementById('nav-notification-badge');
                        if (badge) {
                            if(data.count > 0) {
                                badge.innerText = data.count > 99 ? '99+' : data.count;
                                badge.style.display = 'flex';
                            } else {
                                badge.style.display = 'none';
                            }
                        }

                        // 2. Check for NEW notifications
                        if (data.data && data.data.length > 0) {
                            // Get the most recent notification
                            const latestNotification = data.data[0];
                            
                            // If it's not the first load AND the ID is different from what we saw last time
                            if (!isFirstLoad && lastNotificationId !== latestNotification.id) {
                                // New notification received! Show popup
                                showToast(latestNotification.title, latestNotification.message);
                                
                                // Optional sound
                                // const audio = new Audio('{{ asset("sounds/notification.mp3") }}');
                                // audio.play().catch(e => {});
                            }
                            
                            // Update tracking ID
                            lastNotificationId = latestNotification.id;
                        }
                        
                        isFirstLoad = false;
                    }
                })
                .catch(e => console.error('Notification Polling Error:', e));
            }
            
            // Run on load
            updateNotificationBadge();
            
            // Poll every 5 seconds (Optimized for balance between speed and server load)
            setInterval(updateNotificationBadge, 5000);
        });
    </script>
</body>
</html>
