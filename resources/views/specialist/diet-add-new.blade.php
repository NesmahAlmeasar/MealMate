@extends('layouts.specialist_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'Add New Diet')

{{-- 2. إضافة الـ CSS المضمن (inline) والملفات الخاصة بالصفحة --}}
@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('css/diets-styles.css') }}"> --}} {{-- هذا موجود الآن في التخطيط الرئيسي --}}
    
    {{-- هذا هو الـ CSS المضمن الذي كان في ملفك الأصلي --}}
    <style>
        /* Form Styles */
        .diet-form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--olive-dark);
            font-size: 14px;
        }
        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--olive-medium);
            outline: none;
        }
        .meals-section {
            background-color: var(--olive-very-light);
            border: 1px solid var(--olive-light);
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .meals-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: bold;
            color: var(--olive-dark);
        }
        .meals-list { display: flex; flex-wrap: wrap; gap: 10px; }
        .meal-item {
            background-color: white;
            padding: 8px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .btn-remove-meal {
            background: none; border: none;
            color: var(--red-accent);
            cursor: pointer; font-size: 12px;
            transition: color 0.3s;
        }
        .btn-remove-meal:hover { color: #B91C1C; }
        .modal { display: none; /* ... باقي الستايل ... */ }
        /* (لقد اختصرتُ الستايل هنا لأنه مكرر من الكود الأصلي) */
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border-radius: 10px; width: 80%; max-width: 600px; /* ... */ }
        .close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; position: absolute; top: 10px; right: 20px; cursor: pointer; }
        .close-btn:hover { color: #000; }
        .meal-checkbox-list { max-height: 300px; overflow-y: auto; padding-right: 10px; margin-bottom: 20px; }
        .meal-checkbox-item { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--border-color); cursor: pointer; font-size: 14px; color: var(--text-dark); }
        .meal-checkbox-item:last-child { border-bottom: none; }
        .meal-checkbox-item input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--olive-dark); cursor: pointer; }
        .meal-checkbox-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
        .page-title { font-size: 24px; font-weight: bold; color: var(--text-dark); }
        .diets-header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 10px; background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .btn-edit { background-color: var(--olive-light); color: var(--olive-dark); }
        .btn-edit:hover { background-color: white; }
        .btn-add { background-color: var(--olive-dark); color: white; font-weight: bold; padding: 10px 15px; border-radius: 5px; display: flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: background-color 0.3s ease; }
        .btn-add:hover { background-color: #556B2F; }
    </style>
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')
    <a href="{{ url('specialist/diets') }}" class="back-btn" data-i18n="back">
        <i class="fas fa-arrow-left"></i>
        Back to Diets List
    </a>
    <div class="page-content">
        <div class="diets-header-bar">
            <h1 class="page-title" data-i18n="addNewDiet">Add New Diet</h1>
        </div>

        <form class="diet-form">
            {{-- (محتوى الفورم بالكامل هنا) --}}
            <div class="form-group">
                <label for="diet-name">Diet Name (English)</label>
                <input type="text" id="diet-name" placeholder="e.g., Keto Diet" required>
            </div>
            <div class="form-group">
                <label for="diet-name-ar">Diet Name (Arabic)</label>
                <input type="text" id="diet-name-ar" placeholder="مثال: حمية الكيتو">
            </div>
            <div class="form-group">
                <label for="diet-description">Description (English)</label>
                <textarea id="diet-description" rows="4" placeholder="A brief description..."></textarea>
            </div>
            <div class="form-group">
                <label for="diet-description-ar">Description (Arabic)</label>
                <textarea id="diet-description-ar" rows="4" placeholder="وصف موجز..."></textarea>
            </div>
            <div class="form-group">
                <label for="diet-image">Diet Image URL</label>
                <input type="url" id="diet-image" placeholder="Paste image URL here">
            </div>

            <div class="meals-section">
                <div class="meals-title">
                    <span data-i18n="selectedMeals">Associated Meals</span>
                    <button type="button" class="btn btn-add" id="add-meal-btn">
                        ➕ <span data-i18n="addMeal">Add Meal</span>
                    </button>
                </div>
                <div class="meals-list" id="selected-meals-list">
                    <div class="meal-item">
                        <span>Grilled Chicken Salad</span>
                        <button type="button" class="btn-remove-meal">✕</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-add" style="width: 100%; margin-top: 20px;">
                💾 <span data-i18n="save">Save New Diet</span>
            </button>
        </form>
    </div>

    {{-- 4. المودال (Modal) الخاص بهذه الصفحة --}}
    <div id="meal-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2 data-i18n="allAvailableMeals">Select Meals</h2>
            <div class="meal-checkbox-list">
                {{-- (محتوى المودال بالكامل هنا) --}}
                <label class="meal-checkbox-item">
                    <input type="checkbox" name="meal-select" value="meal1">
                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=100" alt="Grilled Salmon">
                    <span>Grilled Salmon with Asparagus</span>
                </label>
                <label class="meal-checkbox-item">
                    <input type="checkbox" name="meal-select" value="meal2">
                    <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=100" alt="Stir-fry">
                    <span>Vegetable Stir-fry</span>
                </label>
                {{-- ... الخ --}}
            </div>
            <button class="btn btn-add" id="confirm-meals-btn" style="width: 100%; margin-top: 10px;" data-i18n="confirmMeals">
                Add Selected Meals
            </button>
        </div>
    </div>
@endsection

{{-- 5. إضافة الـ JS المضمن (inline) الخاص بهذه الصفحة --}}
@push('scripts')
    <script>
        // كل كود الجافاسكربت المضمن الذي كان في ملفك الأصلي
        const modal = document.getElementById('meal-modal');
        const addMealBtn = document.getElementById('add-meal-btn');
        const closeBtn = document.querySelector('#meal-modal .close-btn'); // استهداف أدق
        const confirmMealsBtn = document.getElementById('confirm-meals-btn');
        const selectedMealsList = document.getElementById('selected-meals-list');

        if (addMealBtn) {
            addMealBtn.onclick = function () {
                if (modal) modal.style.display = 'block';
            }
        }

        if (closeBtn) {
            closeBtn.onclick = function () {
                if (modal) modal.style.display = 'none';
            }
        }

        if (modal) {
            window.addEventListener('click', function (event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }

        if (confirmMealsBtn) {
            confirmMealsBtn.onclick = function () {
                const checkboxes = document.querySelectorAll('.meal-checkbox-list input[type="checkbox"]:checked');
                checkboxes.forEach(checkbox => {
                    const mealName = checkbox.parentNode.querySelector('span').textContent;
                    if (!Array.from(selectedMealsList.children).some(item => item.querySelector('span').textContent === mealName)) {
                        const mealItem = document.createElement('div');
                        mealItem.classList.add('meal-item');
                        mealItem.innerHTML = `
                            <span>${mealName}</span>
                            <button type="button" class="btn-remove-meal">✕</button>
                        `;
                        selectedMealsList.appendChild(mealItem);
                        mealItem.querySelector('.btn-remove-meal').onclick = function (e) {
                            e.preventDefault();
                            mealItem.remove();
                        };
                    }
                    checkbox.checked = false;
                });
                if (modal) modal.style.display = 'none';
            }
        }
        
        // إصلاح الخطأ: هذا الكود كان يستهدف الزر الوحيد الموجود مسبقاً
        document.querySelectorAll('.meal-item .btn-remove-meal').forEach(btn => {
            btn.onclick = function (e) {
                e.preventDefault();
                this.parentNode.remove();
            };
        });
    </script>
@endpush