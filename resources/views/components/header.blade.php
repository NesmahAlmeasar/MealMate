{{-- Header Component مع User Profile Dropdown --}}

<header class="main-header">
    <div class="header-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <div class="header-right">
        {{-- User Profile Dropdown --}}
        <div class="user-profile" onclick="toggleProfileDropdown()">
            <div class="user-avatar">
                @if(auth()->user()->photo_url)
                    <img src="{{ asset('storage/' . auth()->user()->photo_url) }}" alt="Avatar">
                @else
                    <i class="fas fa-user"></i>
                @endif
            </div>
            <div class="user-info">
                <div class="user-name">Hi, {{ auth()->user()->Fname }}</div>
                <div class="user-roles">
                    @foreach(auth()->user()->roles as $role)
                        <span class="role-badge">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
            <i class="fas fa-chevron-down"></i>
        </div>
        
        {{-- Dropdown Menu --}}
        <div id="profileDropdown" class="profile-dropdown" style="display: none;">
            <a href="{{ route('shared.profile') }}" class="dropdown-item">
                <i class="fas fa-user"></i>
                My Profile
            </a>
            <hr style="margin: 0; border-color: #e5e7eb;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

<script>
function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}

// Close dropdown when clicking outside
window.onclick = function(event) {
    if (!event.target.closest('.user-profile')) {
        document.getElementById('profileDropdown').style.display = 'none';
    }
}
</script>

<style>
.main-header {
    height: 70px;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 30px;
    position: fixed;
    top: 0;
    left: 260px;
    right: 0;
    z-index: 999;
}

.header-left .sidebar-toggle {
    background: none;
    border: none;
    font-size: 20px;
    color: #667eea;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 8px;
    transition: background 0.2s;
}

.header-left .sidebar-toggle:hover {
    background: #f3f4f6;
}

.header-right {
    position: relative;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 16px;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.2s;
}

.user-profile:hover {
    background: #f9fafb;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    overflow: hidden;
    font-size: 18px;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 14px;
}

.user-roles {
    display: flex;
    gap: 4px;
    margin-top: 2px;
    flex-wrap: wrap;
}

.role-badge {
    font-size: 10px;
    padding: 2px 6px;
    background: #e0e7ff;
    color: #667eea;
    border-radius: 4px;
    font-weight: 500;
}

.profile-dropdown {
    position: absolute;
    top: 60px;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    min-width: 200px;
    z-index: 1000;
    overflow: hidden;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    color: #374151;
    text-decoration: none;
    transition: background 0.2s;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font-size: 14px;
}

.dropdown-item:hover {
    background: #f9fafb;
}

.dropdown-item.logout {
    color: #dc2626;
}

.dropdown-item.logout:hover {
    background: #fee2e2;
}

/* Responsive */
@media (max-width: 768px) {
    .main-header {
        left: 0;
    }
    
    .user-info {
        display: none;
    }
}
</style>
