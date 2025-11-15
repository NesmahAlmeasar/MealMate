@extends('layouts.specialist_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'Diets Management')

{{-- 2. إضافة الـ CSS المضمن (inline) الخاص بهذه الصفحة --}}
@push('styles')
    <style>
        /* Additional styles for Diets Main Page */
        .page-title {
            font-size: 24px;
            font-weight: bold;
            color: var(--text-dark);
        }
        .diets-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .diets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .diet-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .diet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        .diet-image-container {
            height: 180px;
            overflow: hidden;
        }
        .diet-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .diet-card:hover .diet-image {
            transform: scale(1.05);
        }
        .diet-info { padding: 15px; }
        .diet-name {
            font-size: 18px;
            font-weight: bold;
            color: var(--olive-dark);
            margin-bottom: 5px;
        }
        .diet-description {
            font-size: 13px;
            color: var(--text-light);
            line-height: 1.4;
        }
        .btn-add {
            background-color: var(--olive-dark);
            color: white;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-add:hover { background-color: #556B2F; }
    </style>
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')
    <div class="page-content">
        <div class="diets-header-bar">
            <h1 class="page-title" data-i18n="diets">Available Diets</h1>
            {{-- إصلاح رابط الزر --}}
            <button class="btn btn-add" onclick="window.location.href='{{ url('specialist/diets/add') }}';">
                ➕ <span data-i18n="addNewDiet">Add New Diet</span>
            </button>
        </div>

        <div class="diets-grid">
            <div class="diet-card" onclick="window.location.href='{{ url('specialist/diets/details/1') }}';">
                <div class="diet-image-container">
                    <img src="https://images.unsplash.com/photo-1579613832135-ad9236236b85?q=80&w=1974" alt="Keto Diet" class="diet-image">
                </div>
                <div class="diet-info">
                    <h2 class="diet-name">Keto Diet</h2>
                    <p class="diet-description">Low-carb, high-fat diet that puts the body into a state of ketosis.</p>
                </div>
            </div>

            <div class="diet-card" onclick="window.location.href='{{ url('specialist/diets/details/2') }}';">
                <div class="diet-image-container">
                    <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=2070" alt="Mediterranean Diet" class="diet-image">
                </div>
                <div class="diet-info">
                    <h2 class="diet-name" data-i18n="mediterraneanDiet">Mediterranean Diet</h2>
                    <p class="diet-description">A diet rich in vegetables, fruits, whole grains, and healthy fats.</p>
                </div>
            </div>

            <div class="diet-card" onclick="window.location.href='{{ url('specialist/diets/details/3') }}';">
                <div class="diet-image-container">
                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=2070" alt="Vegan Diet" class="diet-image">
                </div>
                <div class="diet-info">
                    <h2 class="diet-name">Vegan Diet</h2>
                    <p class="diet-description">Excludes all animal products, including meat, dairy, and eggs.</p>
                </div>
            </div>

            <div class="diet-card" onclick="window.location.href='{{ url('specialist/diets/details/4') }}';">
                <div class="diet-image-container">
                    <img src="https://images.unsplash.com/photo-1542826433-2a441312389d?q=80&w=2070" alt="Intermittent Fasting" class="diet-image">
                </div>
                <div class="diet-info">
                    <h2 class="diet-name">Intermittent Fasting</h2>
                    <p class="diet-description">An eating pattern that cycles between periods of eating and fasting.</p>
                </div>
            </div>
        </div>
    </div>
@endsection