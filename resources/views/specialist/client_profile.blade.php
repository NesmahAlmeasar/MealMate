@extends('layouts.specialist_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'Client Profile')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة فقط --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client_profile.css') }}">
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')

    <a href="{{ url('specialist/users') }}" class="back-button" data-i18n="back">
        <i class="fas fa-arrow-left"></i>
        Back to Clients List
    </a>

    <div class="client-profile-section">
        
        <div class="client-info-header card">
            {{-- إصلاح مسار الصورة --}}
            <img src="{{ asset('images/user_male_1.jpg') }}" alt="Client Photo" class="client-large-avatar">
            <div class="client-details">
                <h1 class="client-name">Ahmed Mohammed</h1>
                <span class="diet-tag diet-keto">Keto Diet 🥩</span>
                <div class="client-meta">
                    <span><i class="fas fa-ruler-vertical"></i> 175 cm</span>
                    <span><i class="fas fa-weight-hanging"></i> 85.5 kg</span>
                    <span><i class="fas fa-birthday-cake"></i> 30 years</span>
                </div>
            </div>
            <div class="client-actions">
                {{-- إصلاح مسارات الأزرار (استخدمنا 1 كمثال لـ ID العميل) --}}
                <a href="{{ url('specialist/users/edit/1') }}" class="btn-secondary" data-i18n="edit"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ url('specialist/messages') }}" class="btn-primary"><i class="fas fa-envelope"></i> Message</a>
            </div>
        </div>

        <div class="profile-content-grid">
            
            <div class="card health-info-card">
                <h2 class="card-title" data-i18n="healthMetrics"><i class="fas fa-notes-medical"></i> Health Information</h2>
                <div class="info-group">
                    <strong>Chronic Diseases:</strong>
                    <div class="disease-list-tags">
                        <span class="disease-tag">Diabetes</span>
                        <span class="disease-tag">Hypertension</span>
                    </div>
                </div>
                <div class="info-group">
                    <strong>Current Medications:</strong>
                    <p>Metformin 500mg twice daily, Amlodipine 5mg once daily.</p>
                </div>
                <div class="info-group">
                    <strong>Hormone Status:</strong>
                    <span class="hormone-status active"><i class="fas fa-check-circle"></i> Active (Details: Growth hormone dose daily)</span>
                </div>
            </div>

            <div class="card stats-card">
                <h2 class="card-title"><i class="fas fa-chart-pie"></i> Progress Statistics</h2>
                <div class="stats-grid">
                    {{-- (محتوى الإحصائيات هنا) --}}
                    <div class="stat-item stat-weight">
                        <div class="stat-value">85.5</div>
                        <div class="stat-label">Current Weight (kg)</div>
                    </div>
                    <div class="stat-item stat-loss">
                        <div class="stat-value stat-green">-5.5</div>
                        <div class="stat-label">Weight Lost (kg)</div>
                    </div>
                    <div class="stat-item stat-bmi">
                        <div class="stat-value stat-blue">25.3</div>
                        <div class="stat-label">BMI</div>
                    </div>
                    <div class="stat-item stat-calories">
                        <div class="stat-value stat-red">1500</div>
                        <div class="stat-label">Daily Calories</div>
                    </div>
                </div>
                <div class="chart-placeholder">
                    <i class="fas fa-chart-area"></i>
                    <p>Weight Progress Chart</p>
                </div>
            </div>

            <div class="card meals-card full-width">
                <h2 class="card-title"><i class="fas fa-concierge-bell"></i> Daily Meal Entries</h2>
                <table class="meals-table">
                    <thead>
                        <tr>
                            <th>Meal</th>
                            <th>Entry (From Client)</th>
                            <th>Calories</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Meal"><i class="fas fa-sun"></i> Breakfast</td>
                            <td data-label="Entry">3 fried eggs with olive oil, half an avocado...</td>
                            <td class="calories-value" data-label="Calories">450 Cal</td>
                        </tr>
                        <tr>
                            <td data-label="Meal"><i class="fas fa-cloud-sun"></i> Lunch</td>
                            <td data-label="Entry">Grilled chicken breast (150g), green salad...</td>
                            <td class="calories-value" data-label="Calories">600 Cal</td>
                        </tr>
                        <tr>
                            <td data-label="Meal"><i class="fas fa-moon"></i> Dinner</td>
                            <td data-label="Entry">Grilled salmon (100g), sautéed vegetables.</td>
                            <td class="calories-value" data-label="Calories">350 Cal</td>
                        </tr>
                        <tr>
                            <td data-label="Meal"><i class="fas fa-cookie"></i> Snack/Drinks</td>
                            <td data-label="Entry">Black coffee, dark chocolate (20g).</td>
                            <td class="calories-value" data-label="Calories">100 Cal</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="total-label">Total Daily Calories Entered:</td>
                            <td class="total-calories">1500 Cal</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    {{-- 4. ملاحظة: لقد تجاهلتُ كود الخطأ الذي كان في نهاية ملفك الأصلي --}}
@endsection