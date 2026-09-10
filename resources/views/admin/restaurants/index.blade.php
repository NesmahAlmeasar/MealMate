@extends('layouts.admin_app')

@section('title', 'إدارة المطاعم')

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

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .page-title {
        font-size: 24px;
        font-weight: bold;
        color: var(--text-dark);
        margin: 0;
    }

    /* Buttons */
    .btn-add {
        background-color: var(--olive-dark);
        color: white;
        font-weight: bold;
        padding: 10px 18px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background-color 0.3s ease;
        font-size: 14px;
    }

    .btn-add:hover {
        background-color: #3A5A40;
    }

    /* Grid Layout */
    .restaurants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    /* Card Design */
    .restaurant-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
    }

    .restaurant-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .restaurant-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background-color: var(--olive-very-light);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: var(--olive-medium);
    }

    .restaurant-content {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .restaurant-name {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text-dark);
    }

    .restaurant-meta {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .card-footer {
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 10px;
    }

    .btn-action {
        flex: 1;
        padding: 8px;
        border-radius: 6px;
        text-align: center;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .btn-view {
        background-color: var(--olive-light);
        color: var(--olive-dark);
    }
    
    .btn-view:hover {
        background-color: #E2E8D5;
    }

    .btn-edit {
        background-color: #F3F4F6;
        color: #4B5563;
    }

    .btn-edit:hover {
        background-color: #E5E7EB;
    }

    .btn-delete {
        background-color: #FEE2E2;
        color: var(--red-accent);
        border: none;
        cursor: pointer;
    }

    .btn-delete:hover {
        background-color: #FECACA;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        border: 1px dashed var(--border-color);
    }

    .empty-icon {
        font-size: 4rem;
        color: var(--olive-medium);
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .empty-text {
        color: #666;
        font-size: 1.1rem;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1 class="page-title">إدارة المطاعم</h1>
        @if(Auth::check() && Auth::user()->hasRole('Admin'))
            <a href="{{ route('admin.restaurants.create') }}" class="btn-add">
                <i class="fas fa-plus"></i> إضافة مطعم
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #ECFDF5; color: #065F46; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #A7F3D0;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="restaurants-grid">
        @forelse($restaurants as $restaurant)
            <div class="restaurant-card">
                @if($restaurant->photo_url)
                    <img src="{{ asset('storage/' . $restaurant->photo_url) }}" alt="{{ $restaurant->name }}" class="restaurant-image">
                @else
                    <div class="restaurant-image">
                        <i class="fas fa-store"></i>
                    </div>
                @endif
                
                <div class="restaurant-content">
                    <div class="restaurant-name">{{ $restaurant->name }}</div>
                    
                    <div class="restaurant-meta" style="display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fas fa-utensils" style="color: var(--olive-medium);"></i> {{ $restaurant->meals_count }} وجبات متاحة</span>
                        <span style="background: #ECFDF5; color: #047857; font-weight: 600; padding: 3px 8px; border-radius: 6px; font-size: 0.8rem; border: 1px solid #A7F3D0;">
                            <i class="fas fa-percent"></i> عمولة: {{ number_format($restaurant->commission_rate ?? 10.00, 2) }}%
                        </span>
                    </div>

                    <!-- Optional: Add location/phone summary here if available -->
                    
                    <div class="diet-card-actions" style="justify-content: center; padding-top: 15px; border-top: 1px solid var(--border-color); margin-top: auto;">
                        <a href="{{ route('admin.restaurants.show', $restaurant->restaurants_id) }}" class="diet-action-btn view" title="View">
                            👁️
                        </a>
                        <a href="{{ route('admin.restaurants.edit', $restaurant->restaurants_id) }}" class="diet-action-btn edit" title="Edit">
                            ✏️
                        </a>
                        @if(Auth::check() && Auth::user()->hasRole('Admin'))
                            <form action="{{ route('admin.restaurants.destroy', $restaurant->restaurants_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المطعم؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="diet-action-btn delete" title="Delete">
                                    🗑️
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-store-slash"></i></div>
                <h3 style="color: var(--text-dark); margin-bottom: 10px;">لم يتم العثور على مطاعم</h3>
                
                @if(auth()->user()->hasRole('Restaurant Manager') && !auth()->user()->hasRole('Admin'))
                    <p class="empty-text">ليس لديك أي مطعم مسجل. اطلب من الإدارة إضافة مطعم لك.</p>
                    <button onclick="requestRestaurantCreation(this)" class="btn-add" style="border:none; cursor:pointer;">
                        طلب إضافة مطعم
                    </button>
                @else
                    <p class="empty-text">ابدأ بإضافة مطعمك الأول إلى النظام.</p>
                    <a href="{{ route('admin.restaurants.create') }}" class="btn-add">
                        إضافة مطعم
                    </a>
                @endif
            </div>
        @endforelse
    </div>
</div>

<script>
    function requestRestaurantCreation(btn) {
        if(!confirm('هل تريد إرسال طلب للإدارة لإنشاء مطعم جديد لك؟')) return;

        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
        btn.disabled = true;

        fetch('{{ route("admin.restaurants.request_create") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                btn.innerHTML = '<i class="fas fa-check"></i> تم الإرسال';
                btn.style.backgroundColor = '#10B981'; // Green
            } else {
                alert(data.message || 'حدث خطأ');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@endsection
