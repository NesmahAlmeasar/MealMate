{{-- Sidebar Component للـ Multi-Role Dashboard --}}

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-utensils"></i>
            <span>MealMate</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        {{-- Dashboard (للجميع) --}}
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>لوحة التحكم</span>
        </a>

        {{-- Profile (للجميع) --}}
        <a href="{{ route('shared.profile') }}" class="nav-item {{ request()->routeIs('shared.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>الملف الشخصي</span>
        </a>

        {{-- Admin Section --}}
        @if(auth()->check() && auth()->user()->hasRole('Admin'))
            <div class="nav-section">
                <div class="nav-section-title">
                    <i class="fas fa-shield-alt"></i>
                    لوحة الإدارة العامة
                </div>
                <a href="{{ route('admin.users.index') }}"
                    class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>المستخدمون</span>
                </a>
                <a href="{{ route('admin.restaurants.index') }}"
                    class="nav-item {{ request()->routeIs('admin.restaurants.*') ? 'active' : '' }}">
                    <i class="fas fa-store"></i>
                    <span>إدارة المطاعم والعمولات</span>
                </a>
                <a href="{{ route('admin.meals.index') }}"
                    class="nav-item {{ request()->routeIs('admin.meals.*') ? 'active' : '' }}">
                    <i class="fas fa-utensils"></i>
                    <span>الوجبات</span>
                </a>
                <a href="{{ route('admin.orders.index') }}"
                    class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>إدارة الطلبات</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">
                    <i class="fas fa-wallet"></i>
                    الإدارة المالية والمحاسبة
                </div>
                <a href="{{ route('admin.accounting.index') }}"
                    class="nav-item {{ request()->routeIs('admin.accounting.index') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>الدفتر المحاسبي والقيود</span>
                </a>
                <a href="{{ route('admin.accounting.payouts') }}"
                    class="nav-item {{ request()->routeIs('admin.accounting.payouts') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>طلبات سحب الأرباح</span>
                </a>
                <a href="{{ route('admin.payments.index') }}"
                    class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                    <span>معاملات بوابة BAS</span>
                </a>
            </div>
        @endif

        {{-- Nutrition Manager Section --}}
        @if(auth()->check() && auth()->user()->hasRole('Nutrition Manager'))
            <div class="nav-section">
                <div class="nav-section-title">
                    <i class="fas fa-heartbeat"></i>
                    إدارة التغذية
                </div>
                <a href="{{ route('specialist.meals.pending') }}"
                    class="nav-item {{ request()->routeIs('specialist.meals.pending') ? 'active' : '' }}">
                    <i class="fas fa-clock"></i>
                    <span>وجبات معلقة</span>
                </a>
                <a href="{{ route('nutrition-manager.ingredients.index') }}"
                    class="nav-item {{ request()->routeIs('nutrition-manager.ingredients.*') ? 'active' : '' }}">
                    <i class="fas fa-leaf"></i>
                    <span>المكونات الغذائية</span>
                </a>
                <a href="{{ route('nutrition-manager.chronic-diseases.index') }}"
                    class="nav-item {{ request()->routeIs('nutrition-manager.chronic-diseases.*') ? 'active' : '' }}">
                    <i class="fas fa-disease"></i>
                    <span>الأمراض المزمنة</span>
                </a>
                <a href="{{ route('nutrition-manager.allergies.index') }}"
                    class="nav-item {{ request()->routeIs('nutrition-manager.allergies.*') ? 'active' : '' }}">
                    <i class="fas fa-allergies"></i>
                    <span>الحساسية</span>
                </a>
            </div>
        @endif

        {{-- Specialist Section --}}
        @if(auth()->check() && auth()->user()->hasRole('Specialist'))
            <div class="nav-section">
                <div class="nav-section-title">
                    <i class="fas fa-user-md"></i>
                    أخصائي التغذية
                </div>
                <a href="{{ route('specialist.diets.index') }}"
                    class="nav-item {{ request()->routeIs('specialist.diets.*') ? 'active' : '' }}">
                    <i class="fas fa-apple-alt"></i>
                    <span>الحميات</span>
                </a>
                <a href="{{ route('specialist.dishes') }}"
                    class="nav-item {{ request()->routeIs('specialist.dishes') ? 'active' : '' }}">
                    <i class="fas fa-utensils"></i>
                    <span>الوجبات المعتمدة</span>
                </a>
                <a href="{{ route('chat.index') }}" class="nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i>
                    <span>الاستشارات</span>
                </a>
            </div>
        @endif
    </nav>

    <div class="sidebar-footer">
        <p>&copy; 2025 MealMate</p>
    </div>
</aside>

<style>
    .sidebar {
        width: 260px;
        height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: fixed;
        left: 0;
        top: 0;
        overflow-y: auto;
        transition: transform 0.3s ease;
        z-index: 1000;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 24px;
        font-weight: 700;
    }

    .logo i {
        font-size: 28px;
    }

    .sidebar-nav {
        padding: 20px 0;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .nav-item.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border-left-color: white;
    }

    .nav-item i {
        width: 20px;
        text-align: center;
    }

    .nav-section {
        margin: 20px 0;
    }

    .nav-section-title {
        padding: 10px 20px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.6);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-footer {
        padding: 20px;
        text-align: center;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.5);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }
    }
</style>