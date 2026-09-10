@extends('layouts.admin_app')

@section('title', $restaurant->name)

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    :root {
        --olive-dark: #556B2F;
        --olive-medium: #6B8E23;
        --olive-light: #F0F4E3; 
        --olive-very-light: #F9FAF5;
        --red-accent: #EF4444;
        --border-color: #E5E7EB;
        --text-dark: #1F2937;
    }

    /* Header Profile Card */
    .restaurant-header-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .cover-image {
        height: 200px;
        width: 100%;
        object-fit: cover;
        background-color: var(--olive-dark);
        opacity: 0.9;
    }

    .header-content {
        padding: 20px 30px;
        position: relative;
        display: flex;
        align-items: flex-end;
        gap: 25px;
        margin-top: -50px; /* Overlap effect */
    }

    .logo-container {
        width: 100px;
        height: 100px;
        background: white;
        border-radius: 15px;
        padding: 5px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        z-index: 2;
    }

    .info-container {
        flex-grow: 1;
        padding-bottom: 5px;
        z-index: 2;
    }

    .restaurant-title {
        font-size: 28px;
        font-weight: bold;
        color: var(--text-dark);
        margin: 0;
        text-shadow: 2px 2px 4px rgba(255,255,255,0.8);
    }

    .restaurant-meta {
        color: #666;
        margin-top: 5px;
        font-size: 14px;
        display: flex;
        gap: 15px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        padding-bottom: 10px;
    }

    .btn-edit {
        background: white;
        border: 1px solid var(--border-color);
        color: var(--text-dark);
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-edit:hover {
        background: var(--olive-very-light);
        border-color: var(--olive-medium);
        color: var(--olive-dark);
    }

    /* Content Layout */
    .content-layout {
        display: grid;
        grid-template-columns: 2fr 1fr; /* Main content vs Sidebar */
        gap: 25px;
    }

    .main-section {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 25px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }

    .section-title {
        font-size: 18px;
        font-weight: bold;
        color: var(--olive-dark);
        margin: 0;
    }

    .btn-add-meal {
        background-color: var(--olive-dark);
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: background 0.2s;
    }

    .btn-add-meal:hover {
        background-color: #3A5A40;
    }

    /* Meals List */
    .meals-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .meal-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        transition: all 0.2s;
    }

    .meal-item:hover {
        border-color: var(--olive-medium);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        background-color: var(--olive-very-light);
    }

    .meal-img {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        object-fit: cover;
        background: #eee;
    }

    .meal-details {
        flex-grow: 1;
    }

    .meal-name {
        font-weight: bold;
        color: var(--text-dark);
        font-size: 16px;
    }

    .meal-cat {
        font-size: 12px;
        color: var(--olive-medium);
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 4px;
        display: block;
    }

    .meal-desc {
        font-size: 13px;
        color: #666;
        margin-top: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .meal-price {
        font-weight: bold;
        color: var(--olive-dark);
        font-size: 16px;
    }

    /* Sidebar Info */
    .info-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 20px;
        margin-bottom: 20px;
    }

    .info-title {
        font-weight: bold;
        color: var(--text-dark);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .info-item {
        font-size: 14px;
        color: #555;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #eee;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-icon {
        color: var(--olive-medium);
        margin-top: 3px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    
    <!-- Header Card -->
    <div class="restaurant-header-card">
        @if($restaurant->photo_url)
            <img src="{{ asset('storage/' . $restaurant->photo_url) }}" class="cover-image" alt="Cover">
        @else
            <div class="cover-image"></div>
        @endif

        <div class="header-content">
            <div class="logo-container">
                @if($restaurant->photo_url)
                    <img src="{{ asset('storage/' . $restaurant->photo_url) }}" style="width:100%; height:100%; object-fit:cover; border-radius:10px;">
                @else
                    🍽️
                @endif
            </div>
            
            <div class="info-container">
                <h1 class="restaurant-title">{{ $restaurant->name }}</h1>
                <div class="restaurant-meta">
                    <span class="meta-item"><i class="fas fa-utensils"></i> {{ $restaurant->meals->count() }} وجبة</span>
                    <span class="meta-item"><i class="fas fa-map-marker-alt"></i> {{ $restaurant->location ? '1' : '0' }} فرع</span>
                </div>
            </div>

            <div class="action-buttons">
                <a href="{{ route('admin.restaurants.edit', $restaurant->restaurants_id) }}" class="btn-edit">
                    <i class="fas fa-pen"></i> تعديل الملف الشخصي
                </a>
                <a href="{{ route('admin.restaurants.index') }}" class="btn-edit">
                   العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-layout">
        
        <!-- Left: Menu Items -->
        <div class="main-section">
            <div class="section-header">
                <h2 class="section-title">قائمة الطعام</h2>
                <a href="{{ route('admin.restaurants.create_meal', $restaurant->restaurants_id) }}" class="btn-add-meal">
                    <i class="fas fa-plus"></i> إضافة وجبة
                </a>
            </div>

            <div class="meals-list">
                @forelse($restaurant->meals as $meal)
                <a href="{{ route('admin.meals.edit', $meal->meals_id) }}" class="meal-item" style="text-decoration:none; color:inherit; display:flex;">
                    @if($meal->photo_url)
                        <img src="{{ asset('storage/' . $meal->photo_url) }}" class="meal-img">
                    @else
                        <div class="meal-img" style="display:flex;align-items:center;justify-content:center;font-size:24px;">🍔</div>
                    @endif
                    
                    <div class="meal-details">
                        <span class="meal-cat">{{ $meal->category->category_name ?? 'عام' }}</span>
                        <div class="meal-name">{{ $meal->name }}</div>
                        <div class="meal-desc">{{ $meal->description }}</div>
                    </div>
                    
                    <div class="meal-price">${{ number_format($meal->price, 2) }}</div>
                </a>
                @empty
                <div style="text-align:center; padding:30px; color:#999;">
                    <i class="fas fa-hamburger" style="font-size:30px; margin-bottom:10px;"></i>
                    <p>لم يتم إضافة وجبات بعد.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Info Sidebar -->
        <div class="sidebar-section">
            <!-- Contact Info -->
            <div class="info-card">
                <div class="info-title">
                    <i class="fas fa-phone-alt"></i> أرقام التواصل
                </div>
                <div class="info-list">
                    @forelse($restaurant->phones as $phone)
                        <div class="info-item">
                            <span class="info-icon">📞</span> {{ $phone->phone_number }}
                        </div>
                    @empty
                        <div class="info-item" style="color:#999;">لا توجد أرقام مضافة.</div>
                    @endforelse
                </div>
            </div>

            <!-- Locations -->
            <div class="info-card">
                <div class="info-title">
                    <i class="fas fa-map-marked-alt"></i> المواقع
                </div>
                <div class="info-list">
                    @if($restaurant->location)
                        <div class="info-item">
                            <span class="info-icon">📍</span> {{ $restaurant->location->description ?? $restaurant->location->latitude_x . ', ' . $restaurant->location->longitude_y }}
                        </div>
                    @else
                         <div class="info-item" style="color:#999;">لا يوجد موقع مضاف.</div>
                    @endif
                </div>
            </div>

            <!-- Performance Stats -->
            <div class="info-card" style="background:var(--olive-very-light); border-color:var(--olive-light);">
                <div class="info-title" style="color:var(--olive-dark);">
                    <i class="fas fa-chart-line"></i> الأداء الكلي
                </div>
                <div style="margin-bottom: 5px;">
                    <span style="font-size:24px; font-weight:bold; color:var(--olive-dark);">{{ number_format($totalSales, 2) }}</span>
                    <span style="font-size:14px; color:var(--olive-medium);">ريال</span>
                </div>
                <div style="font-size:14px; color:#555;">
                    <i class="fas fa-shopping-bag"></i> {{ $totalOrders }} طلب مكتمل
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
