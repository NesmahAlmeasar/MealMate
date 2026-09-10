@extends('layouts.admin_app')

@section('title', 'الملف الشخصي')

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
        height: 100%;
    }

    .profile-details-card {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(107, 142, 35, 0.15);
    }

    .form-control[type="file"] {
        padding: 0.75rem;
        cursor: pointer;
    }

    .form-control[type="file"]::-webkit-file-upload-button {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        margin-right: 1rem;
        cursor: pointer;
        transition: background 0.3s;
    }

    .form-control[type="file"]::-webkit-file-upload-button:hover {
        background: var(--primary-dark);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .alert-success {
        background: #d1f7e4;
        color: #27ae60;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: 2px solid #b1f0d1;
        font-weight: 500;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid var(--border-color);
    }

    .btn-update {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        padding: 0.875rem 2.5rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 4px 15px rgba(107, 142, 35, 0.3);
    }

    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(107, 142, 35, 0.4);
    }

    .btn-update:active {
        transform: translateY(0);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-update {
            width: 100%;
            justify-content: center;
        }
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


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error" style="background: #ffe6e6; color: #d32f2f; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid #ffcccc;">
            <strong>يوجد أخطاء في النموذج:</strong>
            <ul style="margin: 0.5rem 0 0 0; padding-right: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('shared.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Main Grid: Personal Info & Specialist Info --}}
        <div class="profile-layout" style="{{ !$nutritionist ? 'display: flex; justify-content: center;' : '' }}">
            
            <!-- Personal Info Card -->
            <div class="info-card" style="{{ !$nutritionist ? 'width: 100%; max-width: 600px;' : '' }}">
                <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--secondary-color); border-bottom: 2px solid #eee; padding-bottom: 10px;">
                    البيانات الشخصية
                </h3>

                <div class="form-group">
                    <label class="form-label">
                        <span>👤</span>
                        الاسم الأول
                    </label>
                    <input type="text" name="Fname" class="form-control" value="{{ old('Fname', $user->Fname) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span>👥</span>
                        الاسم الأخير
                    </label>
                    <input type="text" name="Lname" class="form-control" value="{{ old('Lname', $user->Lname) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span>📧</span>
                        البريد الإلكتروني
                    </label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span>📱</span>
                        رقم الهاتف
                    </label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span>🖼️</span>
                        الصورة الشخصية
                    </label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span>🔑</span>
                        كلمة المرور الجديدة
                    </label>
                    <input type="password" name="password" class="form-control" placeholder="اتركها فارغة إذا لم ترد التغيير">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span>✅</span>
                        تأكيد كلمة المرور
                    </label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="تأكيد كلمة المرور الجديدة">
                </div>
            </div>

            <!-- Specialist Info Card -->
            @if($nutritionist)
            <div class="info-card">
                
                <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--secondary-color); border-bottom: 2px solid #eee; padding-bottom: 10px;">
                    معلومات الأخصائي
                </h3>
                
                <div class="form-group">
                    <label class="form-label">
                        <span>🎓</span>
                        المستوى الأكاديمي
                    </label>
                    <input type="text" name="Academic_level" class="form-control" value="{{ old('Academic_level', $nutritionist->Academic_level) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span>📄</span>
                        الوصف / السيرة الذاتية
                    </label>
                    <textarea name="description" class="form-control" rows="5" style="resize: vertical;">{{ old('description', $nutritionist->description) }}</textarea>
                </div>


                  {{-- Certificates Section (Read-only for Specialist) --}}
    @if($nutritionist && $nutritionist->certificates->isNotEmpty())
        <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--secondary-color); border-bottom: 2px solid #eee; padding-bottom: 10px;">
            الشهادات والتراخيص
        </h3>

        <ul style="list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            @foreach($nutritionist->certificates as $cert)
                <li style="background: white; padding: 20px; border: 1px solid #eee; display: flex; align-items: center; gap: 15px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
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
                </li>
            @endforeach
        </ul>
    @endif

            </div>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-update">
                <span>💾</span>
                حفظ التغييرات
            </button>
        </div>
    </form>

  
</div>
@endsection