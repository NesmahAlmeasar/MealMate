@extends('layouts.admin_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'ملفي الشخصي')

{{-- 2. إضافة CSS الخاص بهذه الصفحة + الـ CSS المضمّن --}}
@push('styles')
    {{-- 
        ملاحظة: لقد افترضت أن ملف 'New folder/styles.css'
        هو نفسه ملف 'public/css/styles.css' الذي لدينا.
        إذا كان اسماً آخر، قم بتغييره هنا.
    --}}
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    
    {{-- هذا هو الـ CSS المضمن (inline) الذي كان في ملفك الأصلي --}}
    <style>
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .back-button-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--text-light);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
            margin-bottom: 15px;
        }
        .back-button-link:hover {
            color: var(--olive-dark);
        }
        .profile-title {
            font-size: 24px;
            font-weight: bold;
            color: var(--text-dark);
        }
        .profile-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }
        .profile-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--border-color);
            text-align: center;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--olive-dark), var(--olive-medium));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            margin: 0 auto 15px;
            color: white;
            border: 4px solid var(--olive-light);
        }
        .profile-name {
            font-size: 22px;
            font-weight: bold;
            color: var(--text-dark);
            margin-bottom: 5px;
        }
        .profile-title-text {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 15px;
        }
        .profile-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }
        .stat-item { text-align: center; }
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: var(--olive-dark);
        }
        .stat-text { font-size: 12px; color: var(--text-light); }
        .profile-details {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--border-color);
        }
        .details-section { margin-bottom: 25px; }
        .details-section:last-child { margin-bottom: 0; }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: var(--text-dark);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--olive-very-light);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label {
            font-weight: 500;
            color: var(--text-dark);
            font-size: 14px;
        }
        .detail-value {
            color: var(--text-light);
            font-size: 14px;
        }
        .profile-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .profile-actions button {
            flex: 1;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        .btn-edit {
            background-color: var(--blue-primary);
            color: white;
        }
        .btn-edit:hover { background-color: #2563EB; }
        .btn-add-info {
            background-color: var(--olive-dark);
            color: white;
        }
        .btn-add-info:hover { background-color: #5a6d1f; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .info-card {
            background-color: var(--olive-very-light);
            padding: 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-card-icon {
            width: 35px;
            height: 35px;
            background-color: var(--olive-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--olive-dark);
            font-size: 16px;
            flex-shrink: 0;
        }
        .info-card-text { font-size: 13px; color: var(--text-dark); }

        /* (باقي كود الـ CSS المضمن هنا) */
        
        /* كود المودال (Modal) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .modal-title { font-size: 18px; font-weight: 600; color: var(--text-dark); }
        .modal-close {
            font-size: 24px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            border: none;
            background: none;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
        }
        .modal-footer {
            text-align: right;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-secondary {
            background-color: #f3f4f6;
            color: #333;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            cursor: pointer;
        }
        .btn-primary {
            background-color: var(--olive-dark);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
        }
    </style>
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')
    
   
    <div class="profile-header">
        <h1 class="profile-title">ملفي الشخصي</h1>
    </div>

    <div class="profile-content">
        <div class="profile-card">
            <div class="profile-image">👨‍⚕️</div>
            <div class="profile-name" id="profileName">Dr. Ahmed Hassan</div>
            <div class="profile-title-text">أخصائي التغذية</div>

            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-number">24</div>
                    <div class="stat-text">العملاء</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">18</div>
                    <div class="stat-text">الحميات</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4.8</div>
                    <div class="stat-text">التقييم</div>
                </div>
            </div>

            <div class="profile-actions">
                <button class="btn-edit" onclick="editProfile()">
                    <i class="fas fa-edit"></i> تعديل الملف الشخصي
                </button>
                <button class="btn-add-info" onclick="openModal('addInfoModal')">
                    <i class="fas fa-plus"></i> إضافة معلومات
                </button>
            </div>
        </div>

        <div class="profile-details">
            <div class="details-section">
                <div class="section-title">المعلومات الشخصية</div>
                <div class="detail-row">
                    <div class="detail-label">الاسم الكامل</div>
                    <div class="detail-value">Dr. Ahmed Hassan</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">البريد الإلكتروني</div>
                    <div class="detail-value">ahmed.hassan@nutrition.com</div>
                </div>
                </div>

            <div class="details-section">
                <div class="section-title">المعلومات المهنية</div>
                <div class="detail-row">
                    <div class="detail-label">التخصص</div>
                    <div class="detail-value">Clinical Nutrition</div>
                </div>
                </div>

            <div class="details-section">
                <div class="section-title">التخصصات</div>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-card-icon">🏋️</div>
                        <div class="info-card-text">Sports Nutrition</div>
                    </div>
                    </div>
            </div>

            <div class="details-section">
                <div class="section-title">نبذة عني</div>
                <p style="color: var(--text-light); font-size: 14px; line-height: 1.6;">
                    Passionate nutrition specialist with over 8 years of experience...
                </p>
            </div>
        </div>
    </div>

    <div id="addInfoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">إضافة معلومات</h2>
                <button class="modal-close" onclick="closeModal('addInfoModal')">×</button>
            </div>
            <form id="addInfoForm" onsubmit="handleAddInfo(event)">
                <div class="form-group">
                    <label for="infoType">نوع المعلومة</label>
                    <select id="infoType" name="infoType" required>
                        <option value="">اختر النوع</option>
                        <option value="certification">شهادة</option>
                        <option value="specialization">تخصص</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="infoTitle">العنوان</label>
                    <input type="text" id="infoTitle" name="infoTitle" placeholder="أدخل العنوان" required>
                </div>
                <div class="form-group">
                    <label for="infoDescription">الوصف</label>
                    <textarea id="infoDescription" name="infoDescription" placeholder="أدخل الوصف" required></textarea>
                </div>
                <div class="form-group">
                    <label for="infoDate">التاريخ</label>
                    <input type="date" id="infoDate" name="infoDate" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('addInfoModal')">إلغاء</button>
                    <button type="submit" class="btn-primary">إضافة المعلومات</button>
                </div>
            </form>
        </div>
    </div>
@endsection


{{-- 5. إضافة الـ JS المضمن الخاص بهذه الصفحة --}}
@push('scripts')
    <script>
        // دوال المودال (Modal)
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // دوال البروفايل
        function editProfile() {
            alert('ميزة تعديل الملف الشخصي قريباً!');
        }

        function handleAddInfo(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('addInfoForm'));
            
            // يمكنك هنا إرسال البيانات للخلفية (backend)
            console.log(Object.fromEntries(formData));
            
            // استخدام دالة التوست (Toast) من app.js
            if (typeof showToast === 'function') {
                showToast('addSuccessfully'); // (افترض أن هذا المفتاح موجود في ملف الترجمة)
            } else {
                alert('تمت إضافة المعلومات بنجاح!');
            }
            
            closeModal('addInfoModal');
            document.getElementById('addInfoForm').reset();
        }

        // (تم حذف كود localStorage لأنه سيتم استبداله ببيانات حقيقية من لارافيل)
    </script>
@endpush