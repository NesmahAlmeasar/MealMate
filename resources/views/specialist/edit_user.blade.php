@extends('layouts.admin_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'تعديل بيانات المستخدم')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة (نفس ملف الإضافة) --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/add_user.css') }}">
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')

    <a href="{{ url('specialist/users') }}" class="back-button" data-i18n="back">
        <i class="fas fa-arrow-left"></i>
        العودة لقائمة العملاء
    </a>

    <div class="add-user-section">
        <h1 class="section-title" data-i18n="editClient">تعديل تفاصيل المستخدم: أحمد محمد</h1>
        
        <div class="card add-user-form-card">
            <form action="#" method="POST" class="user-form">
                
                <div class="form-section-grid">
                    <div class="form-group">
                        <label for="name" data-i18n="fullName">الاسم الكامل</label>
                        {{-- البيانات مسبقة التعبئة --}}
                        <input type="text" id="name" name="name" required value="Ahmed Mohammed">
                    </div>
                    
                    <div class="form-group">
                        <label for="age">العمر (سنة)</label>
                        <input type="number" id="age" name="age" required value="30">
                    </div>

                    <div class="form-group">
                        <label for="height">الطول (سم)</label>
                        <input type="number" id="height" name="height" required value="175">
                    </div>

                    <div class="form-group">
                        <label for="weight">الوزن (كجم)</label>
                        <input type="number" id="weight" name="weight" required step="0.1" value="85.5">
                    </div>
                </div>

                <h2 class="form-subtitle"><i class="fas fa-notes-medical"></i> السجل الطبي</h2>
                
                <div class="form-group full-width disease-group">
                    <div class="medication-header">
                        <label>الأمراض المزمنة (اختر/اكتب)</label>
                        <div class="toggle-switch-small">
                            <input type="checkbox" id="disease-toggle" name="disease_active" class="toggle-input" checked>
                            <label for="disease-toggle" class="toggle-label-small"></label>
                        </div>
                    </div>
                    <div class="diseases-checklist-container" id="diseases-container">
                        <div class="disease-search">
                            <input type="text" id="disease-search" placeholder="بحث أو كتابة مرض جديد...">
                            <i class="fas fa-search search-icon-small"></i>
                        </div>
                        <div class="diseases-checklist">
                            <label><input type="checkbox" name="disease" value="diabetes" checked> Diabetes</label>
                            <label><input type="checkbox" name="disease" value="hypertension" checked> Hypertension</label>
                            <label><input type="checkbox" name="disease" value="heart_disease"> Coronary Heart Disease</label>
                            {{-- ... الخ --}}
                        </div>
                        <textarea id="other-disease-text" name="other_disease_text" rows="2" placeholder="اكتب أمراض أخرى هنا..."></textarea>
                    </div>
                </div>

                <div class="form-group full-width medication-group">
                    <div class="medication-header">
                        <label for="medications">الأدوية الحالية</label>
                        <div class="toggle-switch-small">
                            <input type="checkbox" id="medication-toggle" name="medication_active" class="toggle-input" checked>
                            <label for="medication-toggle" class="toggle-label-small"></label>
                        </div>
                    </div>
                    <textarea id="medications" name="medications" rows="3">Metformin 500mg twice daily, Amlodipine 5mg once daily.</textarea>
                </div>

                <h2 class="form-subtitle"><i class="fas fa-apple-alt"></i> تخصيص الحمية</h2>

                <div class="form-section-grid">
                    <div class="form-group">
                        <label for="diet">الحمية المخصصة</label>
                        <select id="diet" name="diet" required>
                            <option value="">اختر الحمية</option>
                            <option value="keto" selected>Keto Diet</option>
                            <option value="lowcarb">Low-Carb Diet</option>
                            {{-- ... الخ --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="custom-diet-text" id="custom-diet-label" style="display: none;">اسم الحمية المخصصة</label>
                        <input type="text" id="custom-diet-text" name="custom_diet_text" placeholder="أدخل اسم الحمية المخصصة..." style="display: none;">
                    </div>

                    <div class="form-group hormone-group">
                        <div class="hormone-header">
                            <label>تنظيم الهرمونات؟</label>
                            <div class="toggle-switch-small">
                                <input type="checkbox" id="hormones-toggle" name="hormones_active" class="toggle-input" checked>
                                <label for="hormones-toggle" class="toggle-label-small"></label>
                            </div>
                        </div>
                        <textarea id="hormones-details" name="hormones_details" rows="2">Growth hormone dose daily</textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        تحديث بيانات المستخدم
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection