@extends('layouts.specialist_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'Healthy Dishes')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة فقط --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dishes-specific.css') }}">
@endpush


{{-- 3. هذا هو المحتوى المتغير الذي سيتم حقنه في @yield('content') --}}
@section('content')
    <section class="content-section">
        <div class="section-header">
            <h2 class="section-title" id="dishes-title" data-i18n="healthyDishes">Healthy Dishes</h2>
            <button class="recent-meals-btn" id="recent-meals-btn" data-i18n="recentMealsFull">الوجبات المضافه مؤخرا</button>
        </div>

        <div class="dishes-grid">
            {{-- بطاقة 1 --}}
            <div class="dish-card" data-meal-id="1">
                {{-- إصلاح مسار الصورة --}}
                <img src="{{ asset('images/meal_1.jpg') }}" alt="Salmon and Quinoa" class="dish-image">
                <div class="dish-info">
                    <h3 class="dish-title" data-original-text="Grilled Salmon with Quinoa">Grilled Salmon with Quinoa</h3>
                    <div class="dish-details">
                        <span class="calories">450 Kcal</span>
                        <span>Protein: 35g | Carbs: 40g | Fat: 18g</span>
                    </div>
                </div>
            </div>
            
            {{-- بطاقة 2 --}}
            <div class="dish-card" data-meal-id="2">
                {{-- إصلاح مسار الصورة --}}
                <img src="{{ asset('images/meal_2.jpg') }}" alt="Vegan Buddha Bowl" class="dish-image">
                <div class="dish-info">
                    <h3 class="dish-title" data-original-text="Rainbow Veggie Buddha Bowl">Rainbow Veggie Buddha Bowl</h3>
                    <div class="dish-details">
                        <span class="calories">380 Kcal</span>
                        <span>Protein: 15g | Carbs: 55g | Fat: 10g</span>
                    </div>
                </div>
            </div>
            
            {{-- بطاقة 3 --}}
            <div class="dish-card" data-meal-id="3">
                {{-- إصلاح مسار الصورة --}}
                <img src="{{ asset('images/meal_3.jpg') }}" alt="Chicken Salad" class="dish-image">
                <div class="dish-info">
                    <h3 class="dish-title" data-original-text="Mediterranean Chicken Salad">Mediterranean Chicken Salad</h3>
                    <div class="dish-details">
                        <span class="calories">320 Kcal</span>
                        <span>Protein: 30g | Carbs: 20g | Fat: 15g</span>
                    </div>
                </div>
            </div>

            {{-- بطاقة 4 --}}
            <div class="dish-card" data-meal-id="4">
                {{-- إصلاح مسار الصورة --}}
                <img src="{{ asset('images/meal_4.jpg') }}" alt="Steak and Veggies" class="dish-image">
                <div class="dish-info">
                    <h3 class="dish-title" data-original-text="Lean Steak with Roasted Veggies">Lean Steak with Roasted Veggies</h3>
                    <div class="dish-details">
                        <span class="calories">510 Kcal</span>
                        <span>Protein: 45g | Carbs: 30g | Fat: 22g</span>
                    </div>
                </div>
            </div>
            
            {{-- بطاقة 5 --}}
            <div class="dish-card" data-meal-id="5">
                {{-- إصلاح مسار الصورة --}}
                <img src="{{ asset('images/meal_1.jpg') }}" alt="Lentil Soup" class="dish-image">
                <div class="dish-info">
                    <h3 class="dish-title" data-original-text="Hearty Lentil Soup">Hearty Lentil Soup</h3>
                    <div class="dish-details">
                        <span class="calories">250 Kcal</span>
                        <span>Protein: 18g | Carbs: 35g | Fat: 5g</span>
                    </div>
                </div>
            </div>
            
            {{-- بطاقة 6 --}}
            <div class="dish-card" data-meal-id="6">
                {{-- إصلاح مسار الصورة --}}
                <img src="{{ asset('images/meal_2.jpg') }}" alt="Tuna Wrap" class="dish-image">
                <div class="dish-info">
                    <h3 class="dish-title" data-original-text="Whole Wheat Tuna Wrap">Whole Wheat Tuna Wrap</h3>
                    <div class="dish-details">
                        <span class="calories">350 Kcal</span>
                        <span>Protein: 25g | Carbs: 30g | Fat: 10g</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. المودال (Modal) الخاص بهذه الصفحة يبقى معها --}}
    <div id="mealModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div class="modal-header">
                <h2 class="modal-title" id="modal-meal-title">Grilled Salmon with Quinoa</h2>
            </div>
            <div class="modal-body">
                <div class="meal-details-left">
                    {{-- إصلاح مسار الصورة --}}
                    <img src="{{ asset('images/meal_1.jpg') }}" alt="Meal Image" class="modal-image" id="modal-meal-image">
                    <div class="detail-label" data-i18n="description">Description:</div>
                    <p class="detail-value" id="modal-meal-description">A perfect blend...</p>
                    <div class="detail-label" data-i18n="nutritionalFacts">Nutritional Facts:</div>
                    <p class="detail-value">
                        <span id="modal-meal-calories" class="calories">450 Kcal</span> | 
                        Protein: <span id="modal-meal-protein">35g</span> | 
                        Carbs: <span id="modal-meal-carbs">40g</span> | 
                        Fat: <span id="modal-meal-fat">18g</span>
                    </p>
                </div>
                <div class="meal-details-right">
                    <div class="detail-label" data-i18n="components">Components (Check to Modify):</div>
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