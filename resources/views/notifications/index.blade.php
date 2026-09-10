@extends('layouts.admin_app')

@section('title', 'الإشعارات - MealMate')

@section('content')
<div class="main-content-container">
    <div class="page-header">
        <h1 class="page-title">الإشعارات</h1>
        <div class="page-actions">
            <button id="markAllReadBtn" class="btn-primary">
                تحديد الكل كمقروء
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filters-section" style="margin-bottom: 20px;">
        <div class="filter-group">
            <label>تصفية حسب النوع:</label>
            <select id="typeFilter" class="form-select">
                <option value="all">الكل</option>
                <option value="meal_added">إضافة وجبة</option>
                <option value="consultation_booked">حجز استشارة</option>
                <option value="order_submitted">طلب جديد</option>
                <option value="order_status_changed">تحديث الطلب</option>
                <option value="new_message">رسائل</option>
                <option value="appointment_reminder">تذكير</option>
            </select>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="notifications-list">
        @forelse($notifications as $notification)
            <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
                <div class="notification-content">
                    <h4>{{ $notification->title }}</h4>
                    <p>{{ $notification->message }}</p>
                    <div class="notification-meta">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
                <div class="notification-actions">
                    @if(!$notification->is_read)
                        <form action="{{ route('notifications.read', $notification->notification_id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-icon btn-check" title="تحديد كمقروء">✓</button>
                        </form>
                    @endif
                    
                    <form action="{{ route('notifications.destroy', $notification->notification_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon" title="حذف">🗑️</button>
                    </form>
                </div>
            </div>
        @empty
            <p style="text-align:center; color:#888; padding: 20px;">لا توجد إشعارات حالياً.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="pagination-container" style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $notifications->links() }}
    </div>
</div>

@push('styles')
<style>
    .notifications-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .notification-item {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-right: 4px solid transparent;
        transition: all 0.2s;
    }
    .notification-item.unread {
        background: #f0fdf4; /* Light green tint */
        border-right-color: var(--olive-dark);
    }
    .notification-content h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        color: var(--text-dark);
    }
    .notification-content p {
        margin: 0;
        color: var(--text-light);
        font-size: 14px;
    }
    .notification-meta {
        font-size: 12px;
        color: #999;
        margin-top: 5px;
    }
    .notification-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        color: #888;
        font-size: 18px;
        padding: 5px;
        transition: color 0.2s;
    }
    .btn-icon:hover {
        color: var(--red-accent);
    }
    .btn-check:hover {
        color: var(--olive-dark);
    }
    /* Pagination Styles */
    .pagination {
        display: flex;
        list-style: none;
        gap: 5px;
        padding: 0;
    }
    .page-link {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: var(--olive-dark);
        text-decoration: none;
    }
    .page-item.active .page-link {
        background-color: var(--olive-dark);
        color: white;
        border-color: var(--olive-dark);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('markAllReadBtn').addEventListener('click', function() {
            if(confirm('هل تريد تحديد جميع الإشعارات كمقروءة؟')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('notifications.read_all') }}";
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = "{{ csrf_token() }}";
                form.appendChild(csrf);
                
                document.body.appendChild(form);
                form.submit();
            }
        });

        document.getElementById('typeFilter').addEventListener('change', function() {
            const type = this.value;
            const url = new URL(window.location.href);
            if(type === 'all') {
                url.searchParams.delete('type');
            } else {
                url.searchParams.set('type', type);
            }
            window.location.href = url.toString();
        });
        
        // Set initial filter value
        const urlParams = new URLSearchParams(window.location.search);
        const currentType = urlParams.get('type');
        if(currentType) {
            document.getElementById('typeFilter').value = currentType;
        }
    });
</script>
@endpush
@endsection
