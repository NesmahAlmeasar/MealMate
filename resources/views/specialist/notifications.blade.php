@extends('layouts.admin_app')

@section('title', 'الإشعارات')

@section('content')
    <div style="padding: var(--spacing-xl);">
        <a href="{{ url('specialist/dashboard') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: var(--spacing-sm); margin-bottom: var(--spacing-lg);" data-i18n="back">
            <i class="fas fa-arrow-right"></i>
            عودة للشاشة السابقة
        </a>
        
        <div style="max-width: 800px; margin: 0 auto;">
            <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; color: var(--text-dark); margin-bottom: var(--spacing-xl); display: flex; align-items: center; gap: var(--spacing-md);" data-i18n="notificationsTitle">
                <i class="fas fa-bell" style="color: var(--olive-dark);"></i> إشعارات جديدة
            </h1>
            
            <div class="card" style="padding: 0; overflow: hidden;">
                {{-- Notification 1 --}}
                <div style="display: flex; align-items: center; padding: var(--spacing-lg); border-bottom: 1px solid var(--border-color); background: var(--olive-very-light); border-right: 4px solid var(--olive-medium);">
                    <div style="width: 40px; height: 40px; border-radius: var(--radius-full); background: var(--green-success); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; margin-left: var(--spacing-md);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; font-size: 0.95rem; color: var(--text-dark);"><strong>عميل جديد:</strong> تم تسجيل "فاطمة علي" بنجاح.</p>
                        <span style="font-size: 0.8rem; color: var(--text-light); margin-top: 4px; display: block;">منذ 5 دقائق</span>
                    </div>
                    <button class="action-btn view" title="تحديد كمقروء"><i class="fas fa-eye"></i></button>
                </div>

                {{-- Notification 2 --}}
                <div style="display: flex; align-items: center; padding: var(--spacing-lg); border-bottom: 1px solid var(--border-color); background: var(--olive-very-light); border-right: 4px solid var(--red-accent);">
                    <div style="width: 40px; height: 40px; border-radius: var(--radius-full); background: var(--red-accent); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; margin-left: var(--spacing-md);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; font-size: 0.95rem; color: var(--text-dark);"><strong>تنبيه صحي:</strong> سجل "أحمد محمد" انخفاضاً حاداً في الوزن.</p>
                        <span style="font-size: 0.8rem; color: var(--text-light); margin-top: 4px; display: block;">منذ ساعة</span>
                    </div>
                    <button class="action-btn view" title="تحديد كمقروء"><i class="fas fa-eye"></i></button>
                </div>

                {{-- Notification 3 --}}
                <div style="display: flex; align-items: center; padding: var(--spacing-lg); border-bottom: 1px solid var(--border-color);">
                    <div style="width: 40px; height: 40px; border-radius: var(--radius-full); background: var(--blue-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; margin-left: var(--spacing-md);">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; font-size: 0.95rem; color: var(--text-dark);"><strong>رسالة جديدة:</strong> لديك رسالة غير مقروءة من "خالد ناصر".</p>
                        <span style="font-size: 0.8rem; color: var(--text-light); margin-top: 4px; display: block;">منذ يوم</span>
                    </div>
                    <button class="action-btn" style="color: var(--text-light);" title="تحديد كمقروء"><i class="fas fa-eye-slash"></i></button>
                </div>

                <div style="padding: var(--spacing-lg); text-align: center; color: var(--text-light);">
                    <p style="margin: 0;">لا توجد إشعارات أخرى.</p>
                </div>
            </div>
        </div>
    </div>
@endsection