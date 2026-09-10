@extends('layouts.admin_app')

@section('title', 'الحميات - عرض فقط')

@push('styles')
<style>
    .diets-container {
        padding: 20px;
    }
    .diets-header {
        margin-bottom: 30px;
    }
    .diets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }
    .diet-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }
    .diet-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .diet-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .diet-content {
        padding: 20px;
    }
    .diet-title {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }
    .diet-description {
        color: #666;
        font-size: 14px;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .diet-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    .diet-meals-count {
        color: #667eea;
        font-weight: bold;
    }
    .btn-view {
        background: #17a2b8;
        color: white;
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 13px;
    }
    .public-badge {
        background: #28a745;
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
    }
    .private-badge {
        background: #6c757d;
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
    }
</style>
@endpush

@section('content')
<div class="diets-container">
    <div class="diets-header">
        <h1>مكتبة الحميات</h1>
        <p style="color: #666;">عرض جميع خطط الحمية المتاحة (للقراءة فقط)</p>
    </div>

    <div class="diets-grid">
        @forelse($diets as $diet)
            <div class="diet-card" onclick="window.location='{{ route('admin.diets.show', $diet->diets_id) }}'">
                @if($diet->photo_url)
                    <img src="{{ asset('storage/' . $diet->photo_url) }}" alt="{{ $diet->name }}" class="diet-image">
                @else
                    <img src="{{ asset('images/diet_placeholder.jpg') }}" alt="{{ $diet->name }}" class="diet-image">
                @endif
                
                <div class="diet-content">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <h3 class="diet-title">{{ $diet->name }}</h3>
                        <span class="{{ $diet->is_public ? 'public-badge' : 'private-badge' }}">
                            {{ $diet->is_public ? 'عام' : 'خاص' }}
                        </span>
                    </div>
                    
                    <p class="diet-description">{{ $diet->description ?? 'لا يوجد وصف متاح' }}</p>
                    
                    <div class="diet-meta">
                        <span class="diet-meals-count">
                            🍽️ {{ $diet->meals->count() }} وجبات
                        </span>
                        
                        <a href="{{ route('admin.diets.show', $diet->diets_id) }}" class="btn-view" onclick="event.stopPropagation();">عرض التفاصيل</a>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
                <p style="font-size: 18px; color: #666;">لا توجد حميات متاحة</p>
            </div>
        @endforelse
    </div>
</div>
@endsection