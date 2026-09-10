@extends('layouts.admin_app')

@section('title', 'لوحة تحكم المدير')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles-2.css') }}">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .stat-info h3 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: #333;
        }
        .stat-info p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }
        .recent-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .user-list-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .user-list-item:last-child { border-bottom: none; }
        .user-avatar-small {
            width: 40px;
            height: 40px;
            background: #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
            color: #64748b;
        }
    </style>
@endpush

@section('content')
    <div>
        <h1 class="dashboard-title">نظرة عامة</h1>

        @if(session('success'))
            <div class="alert alert-success" style="background: #ECFDF5; color: #065F46; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #A7F3D0;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $totalUsers }}</h3>
                    <p>مجموع المستخدمين</p>
                </div>
                <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $totalSpecialists }}</h3>
                    <p>الأخصائيين</p>
                </div>
                <div class="stat-icon" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-user-md"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $totalMeals }}</h3>
                    <p>مجموع الوجبات</p>
                </div>
                <div class="stat-icon" style="background: #ffedd5; color: #ea580c;">
                    <i class="fas fa-utensils"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $pendingMeals }}</h3>
                    <p>وجبات معلقة</p>
                </div>
                <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $totalRestaurants }}</h3>
                    <p>المطاعم</p>
                </div>
                <div class="stat-icon" style="background: #f3e8ff; color: #9333ea;">
                    <i class="fas fa-store"></i>
                </div>
            </div>
        </div>

        <div class="recent-section">
            <div class="recent-header">
                <h2 style="margin: 0; font-size: 18px; color: #333;">المستخدمين المسجلين حديثاً</h2>
                <a href="{{ route('admin.users.index') }}" style="color: #667eea; text-decoration: none; font-weight: 600; font-size: 14px;">عرض الكل</a>
            </div>
            
            @forelse($recentUsers as $user)
                <div class="user-list-item">
                    <div class="user-avatar-small">
                        {{ strtoupper(substr($user->Fname ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #333;">{{ $user->Fname }} {{ $user->Lname }}</div>
                        <div style="font-size: 13px; color: #888;">{{ $user->email }}</div>
                    </div>
                    <div style="margin-left: auto; font-size: 12px; color: #aaa;">
                        {{ $user->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #999; padding: 20px;">لا يوجد مستخدمين.</p>
            @endforelse
        </div>
    </div>
@endsection