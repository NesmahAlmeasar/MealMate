@extends('layouts.admin_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'Diet Details')

{{-- 2. إضافة الـ CSS المضمن والخاص بالصفحة --}}
@push('styles')
    {{-- هذا ملف CSS أساسي للحميات، موجود مسبقاً في التخطيط --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/diets-styles.css') }}"> --}}

    {{-- هذا هو الـ CSS المضمن الذي كان في ملفك الأصلي --}}
    <style>
        .diet-details-container { display: flex; gap: 20px; }
        .diet-details-info { flex: 2; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
        .diet-details-info h2 { font-size: 20px; color: var(--olive-dark); margin-bottom: 10px; border-bottom: 2px solid var(--olive-light); padding-bottom: 5px; }
        .diet-details-info p { font-size: 14px; line-height: 1.6; color: var(--text-dark); margin-bottom: 20px; }
        .diet-image-details { width: 100%; height: 250px; overflow: hidden; border-radius: 8px; margin-top: 15px; }
        .diet-image-details img { width: 100%; height: 100%; object-fit: cover; }
        .meals-section { flex: 3; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
        .meals-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; margin-top: 15px; }
        .meal-card { background-color: var(--olive-very-light); border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); transition: transform 0.2s; }
        .meal-card:hover { transform: translateY(-3px); }
        .meal-image { width: 100%; height: 120px; overflow: hidden; background-color: #E5E7EB; }
        .meal-info { padding: 10px; }
        .meal-name { font-size: 14px; font-weight: bold; color: var(--olive-dark); margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .meal-restaurant { font-size: 11px; color: var(--text-light); height: 30px; overflow: hidden; text-overflow: ellipsis; }
        .diet-actions { display: flex; gap: 8px; }
        .page-title { font-size: 24px; font-weight: bold; color: var(--text-dark); }
        .diets-header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 10px; background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); }
        .btn-edit { background-color: var(--olive-light); color: var(--olive-dark); font-weight: bold; padding: 10px 15px; border-radius: 5px; display: flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: background-color 0.3s ease; }
        .btn-edit:hover { background-color: white; }
        .btn-delete { background-color: var(--red-accent); color: white; font-weight: bold; padding: 10px 15px; border-radius: 5px; display: flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: background-color 0.3s ease; }
        .btn-delete:hover { background-color: #DC2626; }
        .btn-add { background-color: var(--olive-dark); color: white; font-weight: bold; padding: 10px 15px; border-radius: 5px; display: flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: background-color 0.3s ease; }
        .btn-add:hover { background-color: #556B2F; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border-radius: 10px; width: 80%; max-width: 600px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative; }
        .modal-content h2 { color: var(--olive-dark); margin-bottom: 15px; font-size: 20px; }
        .close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; position: absolute; top: 10px; right: 20px; cursor: pointer; }
        .close-btn:hover { color: #000; }
        .meal-checkbox-list { max-height: 300px; overflow-y: auto; padding-right: 10px; margin-bottom: 20px; }
        .meal-checkbox-item { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--border-color); cursor: pointer; font-size: 14px; color: var(--text-dark); }
        .meal-checkbox-item:last-child { border-bottom: none; }
        .meal-checkbox-item input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--olive-dark); cursor: pointer; }
        .meal-checkbox-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
        @media (max-width: 900px) {
            .diet-details-container { flex-direction: column; }
            .diet-details-info, .meals-section { flex: none; width: 100%; }
        }
    </style>
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')
    <a href="{{ url('admin/diets') }}" class="back-btn" data-i18n="back">
        <i class="fas fa-arrow-left"></i>
        Back to Diets List
    </a>
    <div class="page-content">
        <div class="diets-header-bar">
            <h1 class="page-title" id="diet-details-name">Keto Diet Details</h1>
            <div class="diet-actions">
                {{-- إصلاح الروابط (استخدام 1 كمثال لـ ID) --}}
                <button class="btn btn-edit" onclick="window.location.href='{{ url('specialist/diets/edit/1') }}';" data-i18n="edit">
                    ✏️ Edit Diet
                </button>
                <button class="btn btn-delete" data-i18n="delete">
                    🗑️ Delete Diet
                </button>
            </div>
        </div>

        <div class="diet-details-container">
            <div class="diet-details-info">
                <h2 data-i18n="description">Description</h2>
                <p id="diet-details-description">The ketogenic diet is a very low-carb, high-fat diet...</p>
                <div class="diet-image-details">
                    {{-- الصور الخارجية (External) لا تحتاج asset() --}}
                    <img src="https://images.unsplash.com/photo-1579613832135-ad9236236b85?q=80&w=1974&auto=format&fit=crop" alt="Keto Diet Image">
                </div>
            </div>

            <div class="meals-section">
                <div class="meals-title">
                    <span data-i18n="selectedMeals">Associated Meals (Dishes)</span>
                   
                </div>
                <div class="meals-grid" id="diet-meals-grid">
                    <div class="meal-card">
                        <div class="meal-image">
                            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=300" alt="Keto Salad">
                        </div>
                        <div class="meal-info">
                            <div class="meal-name">Keto Chicken Salad</div>
                            <div class="meal-restaurant">High-fat, low-carb salad...</div>
                        </div>
                    </div>
                    <div class="meal-card">
                        <div class="meal-image">
                            <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=300" alt="Salmon">
                        </div>
                        <div class="meal-info">
                            <div class="meal-name">Grilled Salmon</div>
                            <div class="meal-restaurant">Rich in Omega-3 fatty acids...</div>
                        </div>
                    </div>
                    <div class="meal-card">
                        <div class="meal-image">
                            <img src="https://images.unsplash.com/photo-1542826433-2a441312389d?q=80&w=300" alt="Avocado">
                        </div>
                        <div class="meal-info">
                            <div class="meal-name">Avocado and Eggs</div>
                            <div class="meal-restaurant">A quick, high-fat breakfast...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="meal-modal-details" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2 data-i18n="allAvailableMeals">Select Meals to Add</h2>
            <div class="meal-checkbox-list">
                <label class="meal-checkbox-item">
                    <input type="checkbox" name="meal-select" value="meal1">
                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=100" alt="Salmon">
                    <span>Grilled Salmon with Asparagus</span>
                </label>
                {{-- ... باقي الخيارات ... --}}
            </div>
            <button class="btn btn-add" id="confirm-meals-btn-details" style="width: 100%; margin-top: 10px;" data-i18n="confirmMeals">
                Add Selected Meals
            </button>
        </div>
    </div>
@endsection

{{-- 5. إضافة الـ JS المضمن الخاص بهذه الصفحة --}}
@push('scripts')
    <script>
        (function() { // تغليف الكود
            const modal = document.getElementById('meal-modal-details');
            const addMealBtn = document.getElementById('add-meal-btn-details');
            const closeBtn = document.querySelector('#meal-modal-details .close-btn');
            const confirmMealsBtn = document.getElementById('confirm-meals-btn-details');
            const dietMealsGrid = document.getElementById('diet-meals-grid');

            if (addMealBtn) {
                addMealBtn.onclick = function() {
                    if (modal) modal.style.display = 'block';
                }
            }

            if (closeBtn) {
                closeBtn.onclick = function() {
                    if (modal) modal.style.display = 'none';
                }
            }
            
            if (modal) {
                window.addEventListener('click', function(event) {
                    if (event.target == modal) {
                        modal.style.display = 'none';
                    }
                });
            }

            if (confirmMealsBtn && dietMealsGrid) {
                confirmMealsBtn.onclick = function() {
                    const checkboxes = document.querySelectorAll('#meal-modal-details .meal-checkbox-list input[type="checkbox"]:checked');
                    checkboxes.forEach(checkbox => {
                        const mealName = checkbox.parentNode.querySelector('span').textContent;
                        const mealImageSrc = checkbox.parentNode.querySelector('img').src;
                        
                        const newMealCard = document.createElement('div');
                        newMealCard.classList.add('meal-card');
                        newMealCard.innerHTML = `
                            <div class="meal-image">
                                <img src="${mealImageSrc}" alt="${mealName}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="meal-info">
                                <div class="meal-name">${mealName}</div>
                                <div class="meal-restaurant" data-i18n="newDishAdded">New dish added to the diet.</div>
                            </div>
                        `;
                        dietMealsGrid.appendChild(newMealCard);
                        checkbox.checked = false;
                    });
                    if (modal) modal.style.display = 'none';
                    
                    // !! التحسين: استخدام التوست بدلاً من alert
                    // alert("Meals added successfully!");
                    if (typeof showToast === 'function') {
                        showToast('mealsAddedSuccess');
                    } else {
                        alert('Meals added successfully!');
                    }
                    
                    // إعادة تطبيق الترجمة على العناصر المضافة حديثاً
                    if (typeof updatePageContent === 'function') {
                        updatePageContent();
                    }
                }
            }
        })();
    </script>
@endpush