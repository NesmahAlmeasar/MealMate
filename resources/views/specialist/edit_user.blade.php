@extends('layouts.specialist_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'Edit Client')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة (نفس ملف الإضافة) --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/add_user.css') }}">
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')

    <a href="{{ url('specialist/users') }}" class="back-button" data-i18n="back">
        <i class="fas fa-arrow-left"></i>
        Back to Clients List
    </a>

    <div class="add-user-section">
        <h1 class="section-title" data-i18n="editClient">Edit Client Details: Ahmed Mohammed</h1>
        
        <div class="card add-user-form-card">
            <form action="#" method="POST" class="user-form">
                
                <div class="form-section-grid">
                    <div class="form-group">
                        <label for="name" data-i18n="fullName">Full Name</label>
                        {{-- البيانات مسبقة التعبئة --}}
                        <input type="text" id="name" name="name" required value="Ahmed Mohammed">
                    </div>
                    
                    <div class="form-group">
                        <label for="age">Age (Years)</label>
                        <input type="number" id="age" name="age" required value="30">
                    </div>

                    <div class="form-group">
                        <label for="height">Height (cm)</label>
                        <input type="number" id="height" name="height" required value="175">
                    </div>

                    <div class="form-group">
                        <label for="weight">Weight (kg)</label>
                        <input type="number" id="weight" name="weight" required step="0.1" value="85.5">
                    </div>
                </div>

                <h2 class="form-subtitle"><i class="fas fa-notes-medical"></i> Medical History</h2>
                
                <div class="form-group full-width disease-group">
                    <div class="medication-header">
                        <label>Chronic Diseases (Select/Write)</label>
                        <div class="toggle-switch-small">
                            <input type="checkbox" id="disease-toggle" name="disease_active" class="toggle-input" checked>
                            <label for="disease-toggle" class="toggle-label-small"></label>
                        </div>
                    </div>
                    <div class="diseases-checklist-container" id="diseases-container">
                        <div class="disease-search">
                            <input type="text" id="disease-search" placeholder="Search or write a new disease...">
                            <i class="fas fa-search search-icon-small"></i>
                        </div>
                        <div class="diseases-checklist">
                            <label><input type="checkbox" name="disease" value="diabetes" checked> Diabetes</label>
                            <label><input type="checkbox" name="disease" value="hypertension" checked> Hypertension</label>
                            <label><input type="checkbox" name="disease" value="heart_disease"> Coronary Heart Disease</label>
                            {{-- ... الخ --}}
                        </div>
                        <textarea id="other-disease-text" name="other_disease_text" rows="2" placeholder="Write other diseases here..."></textarea>
                    </div>
                </div>

                <div class="form-group full-width medication-group">
                    <div class="medication-header">
                        <label for="medications">Current Medications</label>
                        <div class="toggle-switch-small">
                            <input type="checkbox" id="medication-toggle" name="medication_active" class="toggle-input" checked>
                            <label for="medication-toggle" class="toggle-label-small"></label>
                        </div>
                    </div>
                    <textarea id="medications" name="medications" rows="3">Metformin 500mg twice daily, Amlodipine 5mg once daily.</textarea>
                </div>

                <h2 class="form-subtitle"><i class="fas fa-apple-alt"></i> Diet Customization</h2>

                <div class="form-section-grid">
                    <div class="form-group">
                        <label for="diet">Assigned Diet</label>
                        <select id="diet" name="diet" required>
                            <option value="">Select Diet</option>
                            <option value="keto" selected>Keto Diet</option>
                            <option value="lowcarb">Low-Carb Diet</option>
                            {{-- ... الخ --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="custom-diet-text" id="custom-diet-label" style="display: none;">Custom Diet Name</label>
                        <input type="text" id="custom-diet-text" name="custom_diet_text" placeholder="Enter custom diet name..." style="display: none;">
                    </div>

                    <div class="form-group hormone-group">
                        <div class="hormone-header">
                            <label>Hormones Regulated?</label>
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
                        Update Client Details
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection