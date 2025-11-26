@extends('layouts.admin_app')

@section('title', 'Pending Meals - Review')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dishes-specific.css') }}">
    <style>
        .approval-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .approve-btn, .reject-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .approve-btn {
            background-color: #28a745;
            color: white;
        }
        .approve-btn:hover {
            background-color: #218838;
        }
        .reject-btn {
            background-color: #dc3545;
            color: white;
        }
        .reject-btn:hover {
            background-color: #c82333;
        }
        .pending-badge {
            background-color: #ffc107;
            color: #000;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <section class="content-section">
        <div class="section-header">
            <h2 class="section-title">الوجبات المضافة مؤخراً - بانتظار المراجعة</h2>
            <a href="{{ route('specialist.dishes') }}" class="recent-meals-btn">
                العودة إلى الوجبات المعتمدة
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="dishes-grid">
            @forelse($pendingMeals as $meal)
                <div class="dish-card" data-meal-id="{{ $meal->meals_id }}">
                    @if($meal->photo_url)
                        <img src="{{ asset('storage/' . $meal->photo_url) }}" alt="{{ $meal->name }}" class="dish-image">
                    @else
                        <img src="{{ asset('images/meal_placeholder.jpg') }}" alt="{{ $meal->name }}" class="dish-image">
                    @endif
                    <div class="dish-info">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 class="dish-title">{{ $meal->name }}</h3>
                            <span class="pending-badge">PENDING</span>
                        </div>
                        <div class="dish-details">
                            <span class="calories">{{ $meal->calories ?? 0 }} Kcal</span>
                            <span>
                                Protein: {{ $meal->protein_g ?? 0 }}g | 
                                Carbs: {{ $meal->carbs_g ?? 0 }}g | 
                                Fat: {{ $meal->fat_g ?? 0 }}g
                            </span>
                        </div>
                        <div class="approval-actions">
                            <form action="{{ route('specialist.meals.approve', $meal->meals_id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="approve-btn" onclick="return confirm('هل أنت متأكد من الموافقة على هذه الوجبة؟')">
                                    ✓ موافقة
                                </button>
                            </form>
                            <form action="{{ route('specialist.meals.reject', $meal->meals_id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="reject-btn" onclick="return confirm('هل أنت متأكد من رفض هذه الوجبة؟')">
                                    ✗ رفض
                                </button>
                            </form>
                            <button class="approve-btn" onclick="viewMealDetails({{ $meal->meals_id }})" style="background-color: #007bff;">
                                👁 عرض التفاصيل
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-meals-message" style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <p style="font-size: 18px; color: #666;">لا توجد وجبات بانتظار المراجعة</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Modal for viewing meal details --}}
    <div id="mealModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div class="modal-header">
                <h2 class="modal-title" id="modal-meal-title">Meal Details</h2>
            </div>
            <div class="modal-body">
                <div class="meal-details-left">
                    <img src="{{ asset('images/meal_placeholder.jpg') }}" alt="Meal Image" class="modal-image" id="modal-meal-image">
                    <div class="detail-label">Description:</div>
                    <p class="detail-value" id="modal-meal-description">Loading...</p>
                    <div class="detail-label">Nutritional Facts:</div>
                    <p class="detail-value">
                        <span id="modal-meal-calories" class="calories">0 Kcal</span> | 
                        Protein: <span id="modal-meal-protein">0g</span> | 
                        Carbs: <span id="modal-meal-carbs">0g</span> | 
                        Fat: <span id="modal-meal-fat">0g</span>
                    </p>
                    <div class="detail-label">Price:</div>
                    <p class="detail-value">$<span id="modal-meal-price">0</span></p>
                    <div class="detail-label">Category:</div>
                    <p class="detail-value" id="modal-meal-category">N/A</p>
                </div>
                <div class="meal-details-right">
                    <div class="detail-label">Ingredients:</div>
                    <ul class="ingredients-list" id="modal-ingredients-list">
                        {{-- Populated by JavaScript --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const modal = document.getElementById('mealModal');
        const closeBtn = document.querySelector('.close-btn');

        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        function viewMealDetails(mealId) {
            fetch(`/specialist/meals/${mealId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const meal = data.meal;
                        
                        document.getElementById('modal-meal-title').textContent = meal.name;
                        document.getElementById('modal-meal-description').textContent = meal.description || 'No description available';
                        document.getElementById('modal-meal-calories').textContent = (meal.calories || 0) + ' Kcal';
                        document.getElementById('modal-meal-protein').textContent = (meal.protein_g || 0) + 'g';
                        document.getElementById('modal-meal-carbs').textContent = (meal.carbs_g || 0) + 'g';
                        document.getElementById('modal-meal-fat').textContent = (meal.fat_g || 0) + 'g';
                        document.getElementById('modal-meal-price').textContent = meal.price || 0;
                        document.getElementById('modal-meal-category').textContent = meal.category || 'N/A';
                        
                        if (meal.photo_url) {
                            document.getElementById('modal-meal-image').src = meal.photo_url;
                        }
                        
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
                                        ${ingredient.is_vegan ? ' | 🌱 Vegan' : ''}
                                        ${ingredient.has_gluten ? ' | ⚠️ Gluten' : ''}
                                        ${ingredient.has_dairy ? ' | 🥛 Dairy' : ''}
                                    </small>
                                `;
                                ingredientsList.appendChild(li);
                            });
                        } else {
                            ingredientsList.innerHTML = '<li>No ingredients listed</li>';
                        }
                        
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
