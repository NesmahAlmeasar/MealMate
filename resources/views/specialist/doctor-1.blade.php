@extends('layouts.specialist_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'About Doctor')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles-2.css') }}">
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')
    <div>
        <div class="doctor-header">
            <h1 class="doctor-name" data-i18n="drAfnanHomude">Dr. Afnan Homude</h1>
        </div>

        <div class="doctor-header"> {{-- (ملاحظة: هذا الكلاس مكرر في الأصل، قد ترغب في مراجعته لاحقاً) --}}
            <div class="doctor-content">
                <div class="doctor-info">
                    <h2 class="doctor-title" data-i18n="drAfnanTitle">Dr. Afnan Hamid – Clinical Nutritionist</h2>
                    
                    <ul class="doctor-list">
                        <li data-i18n="drAfnanPoint1">Licensed and experienced clinical nutritionist</li>
                        <li data-i18n="drAfnanPoint2">Specializes in personalized, science-based nutrition plans</li>
                        <li data-i18n="drAfnanPoint3">Offers expert guidance in weight management, therapeutic diets...</li>
                        <li data-i18n="drAfnanPoint4">Committed to improving clients' health through simple...</li>
                        <li data-i18n="drAfnanPoint5">Known for a caring, supportive, and results-driven approach</li>
                        <li data-i18n="drAfnanPoint6">Provides online consultations tailored to each individual...</li>
                    </ul>
                </div>

                <div class="doctor-image-container">
                    <div class="doctor-image">👩‍⚕️</div>
                </div>
            </div>

            <div class="doctor-footer">
                <button class="btn-add-info" data-i18n="addInfo">Add Info</button>
            </div>
        </div>
    </div>
@endsection