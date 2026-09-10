@extends('layouts.admin_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'الأطباق الصحية')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة فقط --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dishes-specific.css') }}">
@endpush



{{-- 3. هذا هو المحتوى المتغير الذي سيتم حقنه في @yield('content') --}}
@section('content')
    <section style="padding: var(--spacing-xl);">
       <div class="page-header">
        <div>
            <h2>مكتبة الوجبات</h2>
        </div>
        
        @if(isset($canManageMeals) && $canManageMeals)
        <a href="{{ route('admin.meals.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة وجبة جديدة
        </a>
        @endif
    </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: var(--spacing-lg); margin-top: var(--spacing-xl);">
            @forelse($meals as $meal)
                <div class="card" data-meal-id="{{ $meal->meals_id }}" style="cursor: pointer; padding: 0; overflow: hidden;">
                    @if($meal->photo_url)
                        <img src="{{ asset('storage/' . $meal->photo_url) }}" alt="{{ $meal->name }}" style="width: 100%; height: 160px; object-fit: cover;">
                    @else
                        <img src="{{ asset('images/meal_placeholder.jpg') }}" alt="{{ $meal->name }}" style="width: 100%; height: 160px; object-fit: cover; background: var(--bg-gray);">
                    @endif
                    <div style="padding: var(--spacing-md);">
                        <h3 style="font-size: clamp(0.95rem, 1.75vw, 1.1rem); margin-bottom: var(--spacing-sm); color: var(--text-dark);">{{ $meal->name }}</h3>
                        <div style="font-size: clamp(0.7rem, 1.25vw, 0.8rem); color: var(--text-light); margin-bottom: var(--spacing-sm);">
                            <span style="font-weight: 600; color: var(--olive-medium);">{{ $meal->calories ?? 0 }} Kcal</span><br>
                            <span style="font-size: 0.7rem;">
                                P: {{ $meal->protein_g ?? 0 }}g | 
                                C: {{ $meal->carbs_g ?? 0 }}g | 
                                F: {{ $meal->fat_g ?? 0 }}g
                            </span>
                        </div>
                    </div>
                    
                    @if(isset($canManageMeals) && $canManageMeals)
                    <div class="action-buttons" style="justify-content: center; padding: var(--spacing-sm); border-top: 1px solid var(--border-color);">
                        @if(Auth::user()->hasRole('Admin') || (isset($userRestaurant) && $userRestaurant && $meal->restaurants_id == $userRestaurant->restaurants_id))
                        <a href="{{ route('admin.meals.edit', $meal->meals_id) }}" class="action-btn edit" title="تعديل">
                            ✏️
                        </a>
                        @endif
                        
                        @if(Auth::user()->hasRole('Admin'))
                        <form action="{{ route('shared.dishes.destroy', $meal->meals_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الوجبة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete" title="حذف">
                                🗑️
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: var(--spacing-2xl); color: var(--text-light);">
                    <p>لا توجد وجبات معتمدة حالياً</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- 4. المودال (Modal) الخاص بهذه الصفحة --}}
    <div id="mealModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div class="modal-header">
                <h2 class="modal-title" id="modal-meal-title">اسم الوجبة</h2>
            </div>
            <div class="modal-body">
                <div class="meal-details-left">
                    <img src="{{ asset('images/meal_placeholder.jpg') }}" alt="Meal Image" class="modal-image" id="modal-meal-image">
                    <div class="detail-label" data-i18n="description">الوصف:</div>
                    <p class="detail-value" id="modal-meal-description">جاري التحميل...</p>
                    <div class="detail-label" data-i18n="nutritionalFacts">الحقائق الغذائية:</div>
                    <p class="detail-value">
                        <span id="modal-meal-calories" class="calories">0 Kcal</span> | 
                        Protein: <span id="modal-meal-protein">0g</span> | 
                        Carbs: <span id="modal-meal-carbs">0g</span> | 
                        Fat: <span id="modal-meal-fat">0g</span>
                    </p>
                    <div class="detail-label">السعر:</div>
                    <p class="detail-value">
                        $<span id="modal-meal-price">0</span>
                    </p>
                    
                    <div class="detail-label">المطعم:</div>
                    <p class="detail-value" id="modal-meal-restaurant">غير محدد</p>
                    
                    <div class="detail-label">القسم:</div>
                    <p class="detail-value" id="modal-meal-category">غير محدد</p>
                    

                </div>
                <div class="meal-details-right">
                    <div class="detail-label" data-i18n="components">المكونات:</div>
                    <ul class="ingredients-list" id="modal-ingredients-list">
                        {{-- يتم ملؤها بواسطة JS --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Modal functionality
        const modal = document.getElementById('mealModal');
        const closeBtn = document.querySelector('.close-btn');
        const dishCards = document.querySelectorAll('.dish-card');

        // Close modal when clicking X
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Open modal and load meal details
        dishCards.forEach(card => {
            card.addEventListener('click', function() {
                const mealId = this.getAttribute('data-meal-id');
                loadMealDetails(mealId);
            });
        });

        function loadMealDetails(mealId) {
            fetch(`/dishes/${mealId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const meal = data.meal;
                        
                        // Update modal content
                        document.getElementById('modal-meal-title').textContent = meal.name;
                        document.getElementById('modal-meal-description').textContent = meal.description || 'No description available';
                        document.getElementById('modal-meal-calories').textContent = (meal.calories || 0) + ' Kcal';
                        document.getElementById('modal-meal-protein').textContent = (meal.protein_g || 0) + 'g';
                        document.getElementById('modal-meal-carbs').textContent = (meal.carbs_g || 0) + 'g';
                        document.getElementById('modal-meal-fat').textContent = (meal.fat_g || 0) + 'g';
                        document.getElementById('modal-meal-price').textContent = meal.price || 0;
                        
                        // Update image
                        if (meal.photo_url) {
                            document.getElementById('modal-meal-image').src = meal.photo_url;
                        }
                        
                        // Update restaurant and category
                        document.getElementById('modal-meal-restaurant').textContent = meal.restaurant?.name || 'غير محدد';
                        document.getElementById('modal-meal-category').textContent = meal.category?.name || 'غير محدد';

                        
                        // Update ingredients list
                        const ingredientsList = document.getElementById('modal-ingredients-list');
                        ingredientsList.innerHTML = '';
                        
                        if (meal.ingredients && meal.ingredients.length > 0) {
                            meal.ingredients.forEach(ingredient => {
                                const li = document.createElement('li');
                                li.innerHTML = `
                                    <strong>${ingredient.name}</strong>
                                    ${ingredient.quantity ? `(${ingredient.quantity}g)` : ''}
                                `;
                                ingredientsList.appendChild(li);
                            });
                        } else {
                            ingredientsList.innerHTML = '<li>لا توجد مكونات مسجلة</li>';
                        }
                        
                        // Show modal
                        modal.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading meal details:', error);
                    alert('فشل في تحميل تفاصيل الوجبة');
                });
        }
    </script>
    @endpush
@endsection