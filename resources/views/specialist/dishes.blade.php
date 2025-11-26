@extends('layouts.admin_app')

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
            <a href="{{ route('specialist.meals.pending') }}" class="recent-meals-btn" id="recent-meals-btn">
                الوجبات المضافة مؤخراً  
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="dishes-grid">
            @forelse($meals as $meal)
                <div class="dish-card" data-meal-id="{{ $meal->meals_id }}">
                    @if($meal->photo_url)
                        <img src="{{ asset('storage/' . $meal->photo_url) }}" alt="{{ $meal->name }}" class="dish-image">
                    @else
                        <img src="{{ asset('images/meal_placeholder.jpg') }}" alt="{{ $meal->name }}" class="dish-image">
                    @endif
                    <div class="dish-info">
                        <h3 class="dish-title">{{ $meal->name }}</h3>
                        <div class="dish-details">
                            <span class="calories">{{ $meal->calories ?? 0 }} Kcal</span>
                            <span>
                                Protein: {{ $meal->protein_g ?? 0 }}g | 
                                Carbs: {{ $meal->carbs_g ?? 0 }}g | 
                                Fat: {{ $meal->fat_g ?? 0 }}g
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-meals-message">
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
                <h2 class="modal-title" id="modal-meal-title">Meal Name</h2>
            </div>
            <div class="modal-body">
                <div class="meal-details-left">
                    <img src="{{ asset('images/meal_placeholder.jpg') }}" alt="Meal Image" class="modal-image" id="modal-meal-image">
                    <div class="detail-label" data-i18n="description">Description:</div>
                    <p class="detail-value" id="modal-meal-description">Loading...</p>
                    <div class="detail-label" data-i18n="nutritionalFacts">Nutritional Facts:</div>
                    <p class="detail-value">
                        <span id="modal-meal-calories" class="calories">0 Kcal</span> | 
                        Protein: <span id="modal-meal-protein">0g</span> | 
                        Carbs: <span id="modal-meal-carbs">0g</span> | 
                        Fat: <span id="modal-meal-fat">0g</span>
                    </p>
                    <div class="detail-label">Price:</div>
                    <p class="detail-value">
                        $<span id="modal-meal-price">0</span>
                    </p>
                </div>
                <div class="meal-details-right">
                    <div class="detail-label" data-i18n="components">Ingredients:</div>
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
            fetch(`/specialist/meals/${mealId}`)
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
                        
                        // Update ingredients list
                        const ingredientsList = document.getElementById('modal-ingredients-list');
                        ingredientsList.innerHTML = '';
                        
                        if (meal.ingredients && meal.ingredients.length > 0) {
                            meal.ingredients.forEach(ingredient => {
                                const li = document.createElement('li');
                                li.innerHTML = `
                                    <strong>${ingredient.name_ar}</strong>
                                    ${ingredient.quantity_g ? `(${ingredient.quantity_g}g)` : ''}
                                    <br>
                                    <small>
                                        Cal: ${ingredient.calories || 0} | 
                                        P: ${ingredient.protein_g || 0}g | 
                                        C: ${ingredient.carbs_g || 0}g | 
                                        F: ${ingredient.fat_g || 0}g
                                    </small>
                                `;
                                ingredientsList.appendChild(li);
                            });
                        } else {
                            ingredientsList.innerHTML = '<li>No ingredients listed</li>';
                        }
                        
                        // Show modal
                        modal.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading meal details:', error);
                    alert('Failed to load meal details');
                });
        }
    </script>
    @endpush
@endsection