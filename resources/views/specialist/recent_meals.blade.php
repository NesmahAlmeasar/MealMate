@extends('layouts.specialist_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'Recent Meals')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/recent-meals-specific.css') }}">
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')
    <section class="content-section">
        {{-- إصلاح رابط زر الرجوع --}}
        <button class="back-btn" onclick="window.location.href='{{ url('specialist/dishes') }}'">
            <i class="fas fa-arrow-left"></i>
            <span data-i18n="back">Back to Dishes</span>
        </button>

        <div class="section-header">
            <h2 class="section-title" id="recent-meals-title" data-i18n="recentMealsFull">الوجبات المضافة مؤخراً</h2>
        </div>

        <div class="recent-meals-list">
            <div class="meal-item" data-meal-id="101"> 
                {{-- إصلاح مسار الصورة --}}
                <img src="{{ asset('images/meal_1.jpg') }}" alt="Grilled Salmon" class="meal-image-small">
                <div class="meal-details-container">
                    <h3 class="meal-title-recent" data-original-text="Grilled Salmon with Asparagus">Grilled Salmon with Asparagus</h3>
                    <p class="meal-description" data-original-text="A high-protein, low-carb meal.">A high-protein, low-carb meal.</p>
                    <p class="meal-components" data-original-text="Components: Salmon fillet, Asparagus, Lemon, Olive Oil.">Components: Salmon fillet, Asparagus, Lemon, Olive Oil.</p>
                    <p class="meal-calories-recent">Calories: 420 Kcal</p>
                </div>
                <div class="meal-actions">
                    <button class="action-btn check" data-action="add" data-i18n="checkAdd">Check (Add)</button>
                    <button class="action-btn false" data-action="reject" data-i18n="falseReject">False (Reject)</button>
                </div>
            </div>
            
            <div class="meal-item" data-meal-id="102">
                {{-- إصلاح مسار الصورة --}}
                <img src="{{ asset('images/meal_2.jpg') }}" alt="Chicken Quinoa Bowl" class="meal-image-small">
                <div class="meal-details-container">
                    <h3 class="meal-title-recent" data-original-text="Chicken Quinoa Bowl">Chicken Quinoa Bowl</h3>
                    <p class="meal-description" data-original-text="Balanced meal with complex carbs and lean protein.">Balanced meal with complex carbs and lean protein.</p>
                    <p class="meal-components" data-original-text="Components: Chicken breast, Quinoa, Black Beans, Corn, Avocado.">Components: Chicken breast, Quinoa, Black Beans, Corn, Avocado.</p>
                    <p class="meal-calories-recent">Calories: 480 Kcal</p>
                </div>
                <div class="meal-actions">
                    <button class="action-btn check" data-action="add" data-i18n="checkAdd">Check (Add)</button>
                    <button class="action-btn false" data-action="reject" data-i18n="falseReject">False (Reject)</button>
                </div>
            </div>

            <div class="meal-item" data-meal-id="103">
                {{-- إصلاح مسار الصورة --}}
                <img src="{{ asset('images/meal_3.jpg') }}" alt="Lentil Soup" class="meal-image-small">
                <div class="meal-details-container">
                    <h3 class="meal-title-recent" data-original-text="Vegetarian Lentil Soup">Vegetarian Lentil Soup</h3>
                    <p class="meal-description" data-original-text="High in fiber and vegetarian protein.">High in fiber and vegetarian protein.</p>
                    <p class="meal-components" data-original-text="Components: Lentils, Carrots, Celery, Vegetable Broth, Spices.">Components: Lentils, Carrots, Celery, Vegetable Broth, Spices.</p>
                    <p class="meal-calories-recent">Calories: 300 Kcal</p>
                </div>
                <div class="meal-actions">
                    <button class="action-btn check" data-action="add" data-i18n="checkAdd">Check (Add)</button>
                    <button class="action-btn false" data-action="reject" data-i18n="falseReject">False (Reject)</button>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. إضافة المودال (Modal) الخاص بعرض تفاصيل الوجبة (مهم) --}}
    <div id="mealModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div class="modal-header">
                <h2 class="modal-title" id="modal-meal-title">Meal Details</h2>
            </div>
            <div class="modal-body">
                <div class="meal-details-left">
                    <img src="{{ asset('images/meal_1.jpg') }}" alt="Meal Image" class="modal-image" id="modal-meal-image">
                    <div class="detail-label" data-i18n="description">Description:</div>
                    <p class="detail-value" id="modal-meal-description">...</p>
                    <div class="detail-label" data-i18n="nutritionalFacts">Nutritional Facts:</div>
                    <p class="detail-value">
                        <span id="modal-meal-calories" class="calories">...</span> | 
                        Protein: <span id="modal-meal-protein">...</span> | 
                        Carbs: <span id="modal-meal-carbs">...</span> | 
                        Fat: <span id="modal-meal-fat">...</span>
                    </p>
                </div>
                <div class="meal-details-right">
                    <div class="detail-label" data-i18n="components">Components:</div>
                    <ul class="ingredients-list" id="modal-ingredients-list">
                        {{-- يتم ملؤها بواسطة JS --}}
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button class="save-btn" id="save-changes-btn" data-i18n="saveChanges">Save Changes</button>
            </div>
        </div>
    </div>
@endsection