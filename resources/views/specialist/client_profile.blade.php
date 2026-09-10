@extends('layouts.admin_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'ملف المستخدم')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة فقط --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client_profile.css') }}">
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')

    <a href="{{ url('specialist/users') }}" class="back-button" data-i18n="back">
        <i class="fas fa-arrow-left"></i>
        عودة لقائمة العملاء
    </a>

    <div class="client-profile-section">
        
        <div class="client-info-header card">
            {{-- إصلاح مسار الصورة --}}
            <img src="{{ asset('images/user_male_1.jpg') }}" alt="Client Photo" class="client-large-avatar">
            <div class="client-details">
                <h1 class="client-name">Ahmed Mohammed</h1>
                <span class="diet-tag diet-keto">حمية الكيتو 🥩</span>
                <div class="client-meta">
                    <span><i class="fas fa-ruler-vertical"></i> 175 سم</span>
                    <span><i class="fas fa-weight-hanging"></i> 85.5 كجم</span>
                    <span><i class="fas fa-birthday-cake"></i> 30 سنة</span>
                </div>
            </div>
            <div class="client-actions">
                {{-- إصلاح مسارات الأزرار (استخدمنا 1 كمثال لـ ID المستخدم) --}}
                <a href="{{ url('specialist/users/edit/1') }}" class="btn-secondary" data-i18n="edit"><i class="fas fa-edit"></i> تعديل</a>
                <a href="{{ url('specialist/messages') }}" class="btn-primary"><i class="fas fa-envelope"></i> مراسلة</a>
            </div>
        </div>

        <div class="profile-content-grid">
            
            <div class="card health-info-card">
                <h2 class="card-title" data-i18n="healthMetrics"><i class="fas fa-notes-medical"></i> المعلومات الصحية</h2>
                <div class="info-group">
                    <strong>الأمراض المزمنة:</strong>
                    <div class="disease-list-tags">
                        <span class="disease-tag">السكري</span>
                        <span class="disease-tag">ارتفاع ضغط الدم</span>
                    </div>
                </div>
                <div class="info-group">
                    <strong>الأدوية الحالية:</strong>
                    <p>ميتفورمين 500 مجم مرتين يومياً، أملوديبين 5 مجم مرة يومياً.</p>
                </div>
                <div class="info-group">
                    <strong>الحالة الهرمونية:</strong>
                    <span class="hormone-status active"><i class="fas fa-check-circle"></i> نشط (التفاصيل: جرعة هرمون النمو يومياً)</span>
                </div>
            </div>

            <div class="card stats-card">
                <h2 class="card-title"><i class="fas fa-chart-pie"></i> إحصائيات التقدم</h2>
                <div class="stats-grid">
                    {{-- (محتوى الإحصائيات هنا) --}}
                    <div class="stat-item stat-weight">
                        <div class="stat-value">85.5</div>
                        <div class="stat-label">الوزن الحالي (كجم)</div>
                    </div>
                    <div class="stat-item stat-loss">
                        <div class="stat-value stat-green">-5.5</div>
                        <div class="stat-label">الوزن المفقود (كجم)</div>
                    </div>
                    <div class="stat-item stat-bmi">
                        <div class="stat-value stat-blue">25.3</div>
                        <div class="stat-label">مؤشر كتلة الجسم (BMI)</div>
                    </div>
                    <div class="stat-item stat-calories">
                        <div class="stat-value stat-red">1500</div>
                        <div class="stat-label">السعرات اليومية</div>
                    </div>
                </div>
                <div class="chart-placeholder">
                    <i class="fas fa-chart-area"></i>
                    <p>رسم بياني لتقدم الوزن</p>
                </div>
            </div>

            <div class="card meals-card full-width">
                <h2 class="card-title"><i class="fas fa-concierge-bell"></i> إدخالات الوجبات اليومية</h2>
                <table class="meals-table">
                    <thead>
                        <tr>
                            <th>الوجبة</th>
                            <th>الإدخال (من المستخدم)</th>
                            <th>السعرات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Meal"><i class="fas fa-sun"></i> الإفطار</td>
                            <td data-label="Entry">3 بيض مقلي بزيت الزيتون، نصف أفوكادو...</td>
                            <td class="calories-value" data-label="Calories">450 سعرة</td>
                        </tr>
                        <tr>
                            <td data-label="Meal"><i class="fas fa-cloud-sun"></i> الغداء</td>
                            <td data-label="Entry">صدر دجاج مشوي (150 جم)، سلطة خضراء...</td>
                            <td class="calories-value" data-label="Calories">600 سعرة</td>
                        </tr>
                        <tr>
                            <td data-label="Meal"><i class="fas fa-moon"></i> العشاء</td>
                            <td data-label="Entry">سلمون مشوي (100 جم)، خضروات سوتيه.</td>
                            <td class="calories-value" data-label="Calories">350 سعرة</td>
                        </tr>
                        <tr>
                            <td data-label="Meal"><i class="fas fa-cookie"></i> وجبة خفيفة/مشروبات</td>
                            <td data-label="Entry">قهوة سوداء، شوكولاتة داكنة (20 جم).</td>
                            <td class="calories-value" data-label="Calories">100 سعرة</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="total-label">إجمالي السعرات اليومية المدخلة:</td>
                            <td class="total-calories">1500 سعرة</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    {{-- 4. ملاحظة: لقد تجاهلتُ كود الخطأ الذي كان في نهاية ملفك الأصلي --}}
@endsection