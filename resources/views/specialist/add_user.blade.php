@extends('layouts.admin_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'إضافة عميل جديد')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة فقط --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/add_user.css') }}">
    {{-- ملف style.css موجود مسبقاً في التخطيط، لكن إذا كان هذا ملفاً مختلفاً، يمكنك تغيير اسمه وإضافته --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/style.css') }}"> --}}
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')

    <a href="{{ url('specialist/users') }}" class="back-button" data-i18n="back">
        <i class="fas fa-arrow-left"></i>
        العودة لقائمة العملاء
    </a>

    <div class="add-user-section">
        <h1 class="section-title" data-i18n="addNewClient">إضافة تفاصيل عميل جديد</h1>
        
        <div class="card add-user-form-card">
            <form action="#" method="POST" class="user-form">
                
                <div class="form-section-grid">
                    <div class="form-group">
                        <label for="name" data-i18n="fullName">الاسم الكامل</label>
                        <input type="text" id="name" name="name" required placeholder="مثال: أحمد محمد">
                    </div>
                    
                    <div class="form-group">
                        <label for="age">العمر (سنة)</label>
                        <input type="number" id="age" name="age" required min="10" max="100" placeholder="مثال: 30">
                    </div>

                    <div class="form-group">
                        <label for="height">الطول (سم)</label>
                        <input type="number" id="height" name="height" required min="100" max="250" placeholder="مثال: 175">
                    </div>

                    <div class="form-group">
                        <label for="weight">الوزن (كجم)</label>
                        <input type="number" id="weight" name="weight" required min="30" max="300" step="0.1" placeholder="مثال: 85.5">
                    </div>
                </div>

                <h2 class="form-subtitle"><i class="fas fa-notes-medical"></i> Medical History</h2>
                
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
                            <label><input type="checkbox" name="disease" value="diabetes"> Diabetes</label>
                            <label><input type="checkbox" name="disease" value="hypertension"> Hypertension</label>
                            {{-- ... باقي الأمراض ... --}}
                            <label><input type="checkbox" name="disease" value="other" id="other-disease-checkbox"> Other (Write Below)</label>
                        </div>
                        <textarea id="other-disease-text" name="other_disease_text" rows="2" placeholder="اكتب أمراض أخرى هنا..." style="display: none; margin-top: 10px;"></textarea>
                    </div>
                </div>

                <div class="form-group full-width medication-group">
                    <div class="medication-header">
                        <label for="medications">الأدوية الحالية</label>
                        <div class="toggle-switch-small">
                            <input type="checkbox" id="medication-toggle" name="medication_active" class="toggle-input">
                            <label for="medication-toggle" class="toggle-label-small"></label>
                        </div>
                    </div>
                    <textarea id="medications" name="medications" rows="3" placeholder="مثال: ميتفورمين 500 مجم مرتين يومياً..." disabled></textarea>
                </div>

                <h2 class="form-subtitle"><i class="fas fa-apple-alt"></i> تخصيص الحمية</h2>

                <div class="form-section-grid">
                    <div class="form-group">
                        <label for="diet">الحمية المخصصة</label>
                        <select id="diet" name="diet" required>
                            <option value="">اختر الحمية</option>
                            <option value="keto">Keto Diet</option>
                            <option value="lowcarb">Low-Carb Diet</option>
                            <option value="vegan">Vegan Diet</option>
                            <option value="balanced">Balanced Diet</option>
                            <option value="custom">حمية مخصصة (اكتب اسم الحمية)</option>
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
                                <input type="checkbox" id="hormones-toggle" name="hormones_active" class="toggle-input">
                                <label for="hormones-toggle" class="toggle-label-small"></label>
                            </div>
                        </div>
                        <textarea id="hormones-details" name="hormones_details" rows="2" placeholder="أدخل تفاصيل الهرمونات هنا..." disabled></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-user-plus"></i>
                        إضافة المستخدم
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

{{-- 4. لا حاجة لإضافة JS هنا --}}
{{-- ملف app.js الموحّد في التخطيط الرئيسي سيهتم بكل شيء --}}