@extends('layouts.admin_app')

@section('title', 'تفاصيل الطلب #' . $order->cart_id)

@push('styles')
<style>
    .order-details-container {
        padding: var(--spacing-lg);
        max-width: 1400px;
        margin: 0 auto;
    }

    .order-header {
        background: linear-gradient(135deg, var(--olive-medium), var(--olive-dark));
        color: white;
        padding: clamp(1rem, 2.5vw, 1.5rem);
        border-radius: var(--radius-lg);
        margin-bottom: var(--spacing-xl);
        box-shadow: var(--shadow-lg);
    }

    .order-header h1 {
        margin: 0 0 var(--spacing-sm) 0;
        font-size: clamp(1.2rem, 2.5vw, 1.6rem);
        font-weight: bold;
    }

    .order-meta {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    .order-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .order-meta-item i {
        font-size: 18px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .detail-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: clamp(0.875rem, 2vw, 1.25rem);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
    }

    .detail-card h3 {
        margin: 0 0 var(--spacing-md) 0;
        font-size: clamp(0.95rem, 1.75vw, 1.1rem);
        font-weight: bold;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f3f4f6;
    }

    .detail-card h3 i {
        color: #667eea;
        font-size: 20px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: var(--spacing-sm) 0;
        border-bottom: 1px solid var(--bg-gray-dark);
        font-size: clamp(0.75rem, 1.25vw, 0.875rem);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #6b7280;
        font-weight: 500;
    }

    .info-value {
        color: #1f2937;
        font-weight: 600;
        text-align: left;
    }

    .meals-section {
        background: white;
        border-radius: var(--radius-lg);
        padding: clamp(0.875rem, 2vw, 1.25rem);
        box-shadow: var(--shadow-md);
        margin-bottom: var(--spacing-xl);
    }

    .meals-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .meals-table thead {
        background: #f9fafb;
    }

    .meals-table th {
        padding: clamp(0.625rem, 1.75vw, 0.875rem);
        text-align: right;
        font-weight: 600;
        color: var(--text-medium);
        border-bottom: 2px solid var(--border-color);
        font-size: clamp(0.75rem, 1.25vw, 0.875rem);
    }

    .meals-table td {
        padding: clamp(0.625rem, 1.75vw, 0.875rem);
        border-bottom: 1px solid var(--bg-gray-dark);
        font-size: clamp(0.7rem, 1.25vw, 0.8125rem);
    }

    .meals-table tbody tr:hover {
        background: #f9fafb;
    }

    .meal-image {
        width: 50px;
        height: 50px;
        border-radius: var(--radius-md);
        object-fit: cover;
    }

    .meal-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 5px;
    }

    .meal-category {
        font-size: 12px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 3px 10px;
        border-radius: 12px;
        display: inline-block;
    }

    .quantity-badge {
        background: #667eea;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 600;
    }

    .price-cell {
        font-weight: 700;
        color: #059669;
        font-size: 16px;
    }

    .total-section {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        padding: 20px 25px;
        border-radius: 12px;
        margin-top: 20px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
    }

    .total-label {
        font-size: 20px;
        font-weight: bold;
        color: #1f2937;
    }

    .total-value {
        font-size: 28px;
        font-weight: bold;
        color: #059669;
    }

    .status-update-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }

    .status-form {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .status-select {
        flex: 1;
        min-width: 250px;
        padding: 12px 15px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .status-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-update {
        padding: 12px 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #f3f4f6;
        color: #374151;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-back:hover {
        background: #e5e7eb;
        transform: translateX(5px);
    }

    .status-badge {
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        display: inline-block;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-processing {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-completed {
        background: #d1fae5;
        color: #065f46;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .map-container {
        margin-top: 15px;
        height: 200px;
        background: #f3f4f6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #9ca3af;
    }

    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
        }

        .order-meta {
            flex-direction: column;
            gap: 15px;
        }

        .meals-table {
            font-size: 14px;
        }

        .meals-table th,
        .meals-table td {
            padding: 10px;
        }

        .status-form {
            flex-direction: column;
            align-items: stretch;
        }

        .status-select {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="order-details-container">
    <!-- Back Button -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.orders.index') }}" class="btn-back">
            <i class="fas fa-arrow-right"></i>
            العودة إلى قائمة الطلبات
        </a>
    </div>

    <!-- Order Header -->
    <div class="order-header">
        <h1>طلب رقم #{{ $order->cart_id }}</h1>
        <div class="order-meta">
            <div class="order-meta-item">
                <i class="fas fa-calendar"></i>
                <span>{{ $order->date->format('Y-m-d') }}</span>
            </div>
            <div class="order-meta-item">
                <i class="fas fa-clock"></i>
                <span>{{ $order->time }}</span>
            </div>
            <div class="order-meta-item">
                <span class="status-badge status-{{ strtolower($order->state) }}">
                    {{ ucfirst($order->state) }}
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Details Grid -->
    <div class="details-grid">
        <!-- Customer Information -->
        <div class="detail-card">
            <h3>
                <i class="fas fa-user"></i>
                معلومات المستخدم
            </h3>
            @if($order->client && $order->client->user)
                <div class="info-row">
                    <span class="info-label">الاسم</span>
                    <span class="info-value">{{ $order->client->user->Fname }} {{ $order->client->user->Lname }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">رقم الهاتف</span>
                    <span class="info-value">{{ $order->client->user->phone ?? 'غير متوفر' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">البريد الإلكتروني</span>
                    <span class="info-value">{{ $order->client->user->email }}</span>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-user-slash" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <p>معلومات المستخدم غير متوفرة</p>
                </div>
            @endif
        </div>

        <!-- Restaurant Information -->
        <div class="detail-card">
            <h3>
                <i class="fas fa-store"></i>
                معلومات المطعم
            </h3>
            @if($order->restaurant)
                <div class="info-row">
                    <span class="info-label">اسم المطعم</span>
                    <span class="info-value">{{ $order->restaurant->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">رقم الهاتف</span>
                    <span class="info-value">{{ $order->restaurant->phone ?? 'غير متوفر' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">الحالة</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ strtolower($order->restaurant->state) }}">
                            {{ $order->restaurant->state }}
                        </span>
                    </span>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-store-slash" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <p>معلومات المطعم غير متوفرة</p>
                </div>
            @endif
        </div>

        <!-- Delivery Location -->
        <div class="detail-card">
            <h3>
                <i class="fas fa-map-marker-alt"></i>
                موقع التوصيل
            </h3>
            @if($order->location)
                <div class="info-row">
                    <span class="info-label">خط الطول</span>
                    <span class="info-value">{{ $order->location->longitude_y }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">خط العرض</span>
                    <span class="info-value">{{ $order->location->latitude_x }}</span>
                </div>
                @if($order->location->description)
                    <div class="info-row">
                        <span class="info-label">الوصف</span>
                        <span class="info-value">{{ $order->location->description }}</span>
                    </div>
                @endif
                <div class="map-container">
                    <a href="https://www.google.com/maps?q={{ $order->location->latitude_x }},{{ $order->location->longitude_y }}" 
                       target="_blank" 
                       style="color: #667eea; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-external-link-alt"></i> فتح في خرائط جوجل
                    </a>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-map-marked-alt" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <p>موقع التوصيل غير محدد</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Meals Section -->
    <div class="meals-section">
        <h3 style="margin: 0 0 10px 0; font-size: 20px; font-weight: bold; color: #1f2937;">
            <i class="fas fa-utensils" style="color: #667eea;"></i>
            الوجبات المطلوبة
        </h3>
        
        @if($order->items && $order->items->count() > 0)
            <table class="meals-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">الصورة</th>
                        <th>الوجبة</th>
                        <th style="width: 150px; text-align: center;">الكمية</th>
                        <th style="width: 150px; text-align: left;">السعر</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                @if($item->meal && $item->meal->photo_url)
                                    <img src="{{ asset('storage/' . $item->meal->photo_url) }}" 
                                         alt="{{ $item->meal->name }}" 
                                         class="meal-image"
                                         onerror="this.src='{{ asset('images/meal_placeholder.jpg') }}'">
                                @else
                                    <div class="meal-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-utensils" style="color: #9ca3af;"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($item->meal)
                                    <div class="meal-name">{{ $item->meal->name }}</div>
                                    @if($item->meal->category)
                                        <span class="meal-category">{{ $item->meal->category->category_name }}</span>
                                    @endif
                                @else
                                    <span style="color: #9ca3af;">وجبة غير متوفرة</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="quantity-badge">{{ $item->quantity }}</span>
                            </td>
                            <td class="price-cell">
                                @if($item->meal)
                                    ${{ number_format($item->meal->price * $item->quantity, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Total Section -->
            <div class="total-section">
                <div class="total-row">
                    <span class="total-label">الإجمالي الكلي</span>
                    <span class="total-value">${{ number_format($order->total_price, 2) }}</span>
                </div>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-shopping-basket" style="font-size: 50px; margin-bottom: 15px;"></i>
                <p style="font-size: 16px;">لا توجد وجبات في هذا الطلب</p>
            </div>
        @endif
    </div>

    <!-- Status Update Section -->
    <div class="status-update-section">
        <h3 style="margin: 0 0 20px 0; font-size: 18px; font-weight: bold; color: #1f2937;">
            <i class="fas fa-edit" style="color: #667eea;"></i>
            تحديث حالة الطلب
        </h3>
        
        <form action="{{ route('admin.orders.updateStatus', $order->cart_id) }}" method="POST" class="status-form">
            @csrf
            <select name="state" class="status-select" required>
                <option value="pending" {{ $order->state == 'pending' ? 'selected' : '' }}>قيد الانتظار (Pending)</option>
                <option value="processing" {{ $order->state == 'processing' ? 'selected' : '' }}>قيد المعالجة (Processing)</option>
                <option value="completed" {{ $order->state == 'completed' ? 'selected' : '' }}>مكتمل (Completed)</option>
                <option value="cancelled" {{ $order->state == 'cancelled' ? 'selected' : '' }}>ملغي (Cancelled)</option>
            </select>
            <button type="submit" class="btn-update">
                <i class="fas fa-check"></i>
                تحديث الحالة
            </button>
        </form>
    </div>
</div>
@endsection
