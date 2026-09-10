@extends('layouts.admin_app')

@section('title', 'ملف المستخدم')

@push('styles')
<style>
    :root {
        --primary-color: #6B8E23;
        --primary-dark: #556B2F;
        --primary-light: #9ABC60;
        --secondary-color: #2c3e50;
        --light-bg: #f8f9fa;
        --border-color: #e9ecef;
        --text-dark: #34495e;
        --text-light: #7f8c8d;
    }

    .profile-container {
        max-width: min(1200px, 95%);
        margin: 0 auto;
        padding: clamp(1rem, 3vw, 2rem);
    }

    /* New Header styling */
    .profile-main-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .profile-avatar-container {
        width: 140px;
        height: 140px;
        margin: 0 auto 1rem;
        position: relative;
    }

    .profile-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        color: var(--primary-color);
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .profile-name {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        color: var(--secondary-color);
    }

    .profile-role {
        background: #e8f5e9;
        color: var(--primary-dark);
        padding: 0.5rem 1.5rem;
        border-radius: 30px;
        font-size: 0.95rem;
        font-weight: 600;
        display: inline-block;
    }

    .profile-layout {
        display: grid;
        /* Default to equal columns if nutritionist data exists */
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: clamp(1.5rem, 4vw, 2.5rem);
        align-items: start;
        margin-bottom: 2.5rem;
    }
    
    .info-card {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        height: 100%; /* Ensure equal height */
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: rgba(0, 0, 0, 0.02);
        border-radius: 12px;
    }

    .info-content {
        text-align: right;
        flex: 1;
    }

    .info-label {
        font-size: 0.8rem;
        opacity: 0.7;
        display: block;
        margin-bottom: 2px;
    }

    .info-value {
        font-weight: 600;
        font-size: 1rem;
        color: var(--text-dark);
    }

    .detail-group {
        margin-bottom: 1.5rem;
    }

    .detail-label {
        font-weight: bold;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        display: block;
    }

    .profile-details-card {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="profile-container">
    
    {{-- Top Header: Image, Name, Role --}}
    <div class="profile-main-header">
        <div class="profile-avatar-container">
            @if($user->photo_url)
                <img src="{{ asset('storage/' . $user->photo_url) }}" alt="{{ $user->Fname }}" class="profile-avatar">
            @else
                <div class="avatar-placeholder">{{ substr($user->Fname, 0, 1) }}</div>
            @endif
        </div>
        
        <div class="profile-name">{{ $user->Fname }} {{ $user->Lname }}</div>
        
        <div class="profile-role">
            @if($user->roles->isNotEmpty())
                {{ $user->roles->pluck('name')->join(', ') }}
            @else
                مستخدم
            @endif
        </div>
    </div>

    {{-- Main Grid: Personal Info & Specialist Info --}}
    <div class="profile-layout" style="{{ !$nutritionist ? 'display: flex; justify-content: center;' : '' }}">
        
        <!-- Personal Info Card -->
        <div class="info-card" style="{{ !$nutritionist ? 'width: 100%; max-width: 600px;' : '' }}">
            <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--secondary-color); border-bottom: 2px solid #eee; padding-bottom: 10px;">
                البيانات الشخصية
            </h3>

            <div class="profile-info">
                <div class="info-item">
                    <span style="font-size: 1.2rem">📧</span>
                    <div class="info-content">
                        <span class="info-label">البريد الإلكتروني</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                </div>
                
                @if($user->phone)
                <div class="info-item">
                    <span style="font-size: 1.2rem">📱</span>
                    <div class="info-content">
                        <span class="info-label">رقم الهاتف</span>
                        <span class="info-value">{{ $user->phone }}</span>
                    </div>
                </div>
                @endif
                
                <div class="info-item">
                    <span style="font-size: 1.2rem;">ℹ️</span>
                    <div class="info-content">
                       <span class="info-label">حالة الحساب</span>
                       @if($user->account_state == 'active' || $user->account_state == 'Active')
                           <span style="color: #28a745; font-weight: bold;">نشط</span>
                       @else
                           <span style="color: #dc3545; font-weight: bold;">غير نشط</span>
                       @endif
                    </div>
                </div>

                <div class="info-item">
                    <span style="font-size: 1.2rem">📅</span>
                    <div class="info-content">
                        <span class="info-label">تاريخ الانضمام</span>
                        <span class="info-value">{{ $user->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <a href="{{ route('admin.messages') }}?user={{ $user->user_id }}" class="btn-primary" style="background: var(--secondary-color); color: white; border: none; padding: 12px 20px; border-radius: 10px; cursor: pointer; display: block; text-decoration: none; text-align: center; font-weight: bold; transition: 0.2s;">
                    <i class="fas fa-comment" style="margin-left: 5px;"></i> إرسال رسالة
                </a>
            </div>
        </div>

        <!-- Specialist Info Card -->
        @if($nutritionist)
        <div class="info-card">
            
            <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--secondary-color); border-bottom: 2px solid #eee; padding-bottom: 10px;">
                معلومات الأخصائي
            </h3>
            
            <form action="{{ route('admin.users.update_specialist', $user->user_id) }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="detail-label">المستوى الأكاديمي</label>
                    <input type="text" name="Academic_level" class="form-control" value="{{ $nutritionist->Academic_level }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label class="detail-label">نبذة / الوصف</label>
                    <textarea name="description" class="form-control" rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; resize: vertical;">{{ $nutritionist->description }}</textarea>
                </div>

                <button type="submit" class="btn-primary" style="background: var(--primary-color); color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; font-size: 1rem;">
                    حفظ التعديلات
                </button>
            </form>

        </div>
        @endif
    </div>

    <!-- Certificates Section (Full Width) -->
    @if($nutritionist)
    <div class="profile-details-card" style="margin-top: 10px;">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--secondary-color); border-bottom: 2px solid #eee; padding-bottom: 10px;">
            الشهادات والتراخيص
        </h3>

        @if($nutritionist->certificates->isNotEmpty())
            <ul style="list-style: none; padding: 0; margin-bottom: 30px; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                @foreach($nutritionist->certificates as $cert)
                    <li style="background: white; padding: 20px; border: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 60px; height: 60px; flex-shrink: 0;">
                                @if($cert->photo_url)
                                    <a href="{{ asset('storage/' . $cert->photo_url) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $cert->photo_url) }}" alt="Certificate" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
                                    </a>
                                @else
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f0f0f0; border-radius: 8px; font-size: 2rem;">📜</div>
                                @endif
                            </div>
                            <div>
                                <div style="font-weight: bold; font-size: 1.05rem; margin-bottom: 4px;">{{ $cert->certificate_type }}</div>
                                <div style="font-size: 0.85rem; color: #888;">{{ $cert->created_at->format('Y-m-d') }}</div>
                            </div>
                        </div>
                        <form action="{{ route('admin.users.delete_certificate', $cert->certificate_id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الشهادة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: #fff0f0; color: #dc3545; border: 1px solid #dc3545; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.9rem; transition: 0.2s;">
                                حذف
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @else
            <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 12px; margin-bottom: 30px;">
                <p style="color: #666; font-size: 1.1rem;">لا توجد شهادات مضافة حالياً.</p>
            </div>
        @endif

        {{-- Add Certificate Form --}}
        <div style="background: #f8fff9; padding: 25px; border-radius: 15px; border: 1px solid #c3e6cb;">
            <h4 style="margin-top: 0; margin-bottom: 20px; color: #155724; display: flex; align-items: center; gap: 10px; font-size: 1.1rem;">
                <i class="fas fa-plus-circle"></i> إضافة شهادة جديدة
            </h4>
            <form action="{{ route('admin.users.add_certificate', $user->user_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
                    <div style="flex: 2; min-width: 250px;">
                        <label class="detail-label" style="font-size: 0.9rem; margin-bottom: 8px;">نوع الشهادة / الاسم</label>
                        <input type="text" name="certificate_type" class="form-control" required placeholder="مثال: بكالوريوس تغذية علاجية" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    </div>
                    
                    <div style="flex: 1; min-width: 250px;">
                        <label class="detail-label" style="font-size: 0.9rem; margin-bottom: 8px;">صورة الشهادة (اختياري)</label>
                        <input type="file" name="certificate_file" class="form-control" accept="image/*,application/pdf" style="width: 100%; padding: 10px; background: white; border: 1px solid #ddd; border-radius: 8px;">
                    </div>

                    <button type="submit" class="btn-primary" style="background: #198754; color: white; border: none; padding: 10px 30px; border-radius: 8px; cursor: pointer; height: 42px; font-weight: bold; font-size: 1rem;">
                        إضافة
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
