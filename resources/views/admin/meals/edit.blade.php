@extends('layouts.admin_app')

@section('title', 'تعديل الوجبة: ' . $meal->name)

@push('styles')
<style>
    /* Form Styles from User Request */
    .diet-form {
        background-color: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        max-width: 1000px;
        margin: 20px auto;
    }

    .form-header {
        margin-bottom: 30px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 15px;
    }
    
    .form-header h1 {
        color: var(--olive-dark, #333);
        font-size: 24px;
        margin-bottom: 10px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--olive-dark, #555);
        font-size: 14px;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="url"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color, #ddd);
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--olive-medium, #667eea);
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .input-group {
        display: flex;
        gap: 10px;
    }

    .btn-add-category {
        background-color: var(--olive-dark, #28a745);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0 15px;
        cursor: pointer;
        font-weight: 600;
        white-space: nowrap;
    }

    .ingredients-section {
        grid-column: 1 / -1;
        background-color: var(--olive-very-light, #f8f9fa);
        border: 1px solid var(--olive-light, #e9ecef);
        padding: 20px;
        border-radius: 12px;
        margin-top: 20px;
    }

    .ingredient-item {
        display: grid;
        grid-template-columns: 3fr 1fr auto;
        gap: 15px;
        margin-bottom: 15px;
        align-items: end;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .add-ingredient-btn {
        background-color: var(--olive-dark, #667eea);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        margin-top: 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .remove-ingredient-btn {
        background: #fee2e2;
        color: #dc2626;
        padding: 10px 15px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.2s;
    }
    
    .remove-ingredient-btn:hover {
        background: #fecaca;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .btn-submit {
        background: rgba(107, 142, 35, 0.15);
        color: #556B2F;
        border: 2px solid #6B8E23;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(107, 142, 35, 0.2);
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background: #6B8E23;
        color: white;
        box-shadow: 0 6px 20px rgba(107, 142, 35, 0.4);
        transform: translateY(-2px);
    }

    .btn-cancel {
        background: #f1f5f9;
        color: #64748b;
        padding: 12px 30px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        font-weight: 600;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
        display: block;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 30px;
        border-radius: 15px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        position: relative;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .modal-header h3 {
        margin: 0;
        color: #333;
    }

    .close-modal {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        line-height: 1;
    }

    .close-modal:hover {
        color: #000;
    }

    /* Read-only inputs for calculated values */
    .calculated-input {
        background-color: #f8fafc;
        cursor: not-allowed;
    }
    
    .current-photo {
        margin-bottom: 15px;
        text-align: center;
    }
    .current-photo img {
        max-width: 200px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="diet-form">
    <div class="form-header">
        <h1>تعديل الوجبة: {{ $meal->name }}</h1>
        <p>قم بتحديث التفاصيل أدناه. سيتم إعادة حساب القيم الغذائية في حال تغيير المكونات.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" style="background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.meals.update', $meal->meals_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <!-- Meal Name -->
            <div class="form-group">
                <label for="name">اسم الوجبة *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $meal->name) }}" required>
                @error('name') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            
            <!-- Description -->
            <div class="form-group full-width">
                <label for="description">الوصف</label>
                <textarea id="description" name="description" rows="3">{{ old('description', $meal->description) }}</textarea>
            </div>


            <!-- Restaurant Selection -->
            <div class="form-group">
                <label for="restaurant_id">المطعم</label>
                <select id="restaurant_id" name="restaurant_id">
                    <option value="">اختر المطعم (اختياري)</option>
                    @foreach($restaurants as $resto)
                        <option value="{{ $resto->restaurants_id }}" {{ old('restaurant_id', $meal->restaurant_id) == $resto->restaurants_id ? 'selected' : '' }}>
                            {{ $resto->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Category with Add New Button -->
            <div class="form-group">
                <label for="category_input">الفئة</label>
                <div class="input-group">
                    <input list="categories_list" id="category_input" class="form-control" placeholder="اكتب للبحث أو إضافة جديد..." 
                           value="{{ $meal->category ? $meal->category->category_name : '' }}">
                    <datalist id="categories_list">
                        @foreach($categories as $category)
                            <option value="{{ $category->category_name }}" data-id="{{ $category->category_id }}">
                        @endforeach
                    </datalist>
                    <input type="hidden" name="category_id" id="category_id" value="{{ old('category_id', $meal->category_id) }}">
                    <button type="button" class="btn-add-category" onclick="openModal()">
                        <i class="fas fa-plus"></i> جديد
                    </button>
                </div>
                @error('category_id') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Price -->
            <div class="form-group">
                <label for="price">السعر ($) *</label>
                <input type="number" id="price" name="price" step="0.01" value="{{ old('price', $meal->price) }}" required>
                @error('price') <span class="error-message">{{ $message }}</span> @enderror
            </div>



            <!-- Preparation Time -->
            <div class="form-group">
                <label for="preparation_time">وقت التحضير (دقائق)</label>
                <input type="number" id="preparation_time" name="preparation_time" min="1" value="{{ old('preparation_time', $meal->preparation_time) }}">
            </div>

           
            <!-- Photo -->
            <div class="form-group">
                <label for="photo" class="form-label">صورة الوجبة</label>
                @if($meal->photo_url)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $meal->photo_url) }}" alt="{{ $meal->name }}" style="max-width: 150px; border-radius: 8px;">
                    </div>
                @endif
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                @error('photo') <span class="error-message">{{ $message }}</span> @enderror
            </div>
            

  <!-- Ingredients Section -->
            <div class="ingredients-section">
                <h3 style="margin-top: 0; margin-bottom: 15px;">المكونات</h3>
                <div id="ingredients-container">
                    <!-- Template for JS -->
                </div>
                
                <button type="button" class="add-ingredient-btn" onclick="addIngredient()">
                    <i class="fas fa-plus"></i> إضافة مكون
                </button>
            </div>


              <!-- Quantity -->
            <div class="form-group">
                <label for="quantity_g">الكمية الكلية (غ)</label>
                <input type="number" id="quantity_g" name="quantity_g" step="0.01" value="{{ old('quantity_g', $meal->quantity_g) }}" class="calculated-input" readonly>
                <small style="color: #666;">يتم حسابها تلقائياً من المكونات</small>
            </div>

            <!-- Nutritional Info (Calculated) -->
            <div class="form-group full-width">
                <h3 style="margin-bottom: 15px; color: #333; font-size: 18px;">المعلومات الغذائية (محسوبة)</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="calories">سعرات حرارية</label>
                        <input type="number" id="calories" name="calories" step="0.01" value="{{ old('calories', $meal->calories) }}" class="calculated-input" readonly>
                    </div>
                    <div class="form-group">
                        <label for="protein_g">بروتين (غ)</label>
                        <input type="number" id="protein_g" name="protein_g" step="0.01" value="{{ old('protein_g', $meal->protein_g) }}" class="calculated-input" readonly>
                    </div>
                    <div class="form-group">
                        <label for="fat_g">دهون (غ)</label>
                        <input type="number" id="fat_g" name="fat_g" step="0.01" value="{{ old('fat_g', $meal->fat_g) }}" class="calculated-input" readonly>
                    </div>
                    <div class="form-group">
                        <label for="carbs_g">كربوهيدرات (غ)</label>
                        <input type="number" id="carbs_g" name="carbs_g" step="0.01" value="{{ old('carbs_g', $meal->carbs_g) }}" class="calculated-input" readonly>
                    </div>
                </div>
            </div>

          
          
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">تحديث الوجبة</button>
            <a href="{{ route('admin.meals.index') }}" class="btn-cancel">إلغاء</a>
        </div>
    </form>
</div>

<!-- Add Category Modal -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>إضافة فئة جديدة</h3>
            <span class="close-modal" onclick="closeModal()">&times;</span>
        </div>
        <div class="form-group">
            <label>اسم الفئة</label>
            <input type="text" id="new_category_name" placeholder="مثال: مقبلات">
            <span id="category-error" class="error-message" style="display: none;"></span>
        </div>
        <button type="button" class="btn-submit" onclick="saveCategory()" style="width: 100%;">حفظ الفئة</button>
    </div>
</div>

@push('scripts')
<script>
    // Ingredients Data for Calculation
    const ingredientsData = @json($ingredients);
    const existingIngredients = @json($meal->ingredients);
    
    // Initial Setup
    document.addEventListener('DOMContentLoaded', function() {
        // Load existing ingredients
        if (existingIngredients.length > 0) {
            existingIngredients.forEach(ing => {
                addIngredient(ing.ingredients_id, ing.pivot.quantity_g);
            });
        } else {
            addIngredient();
        }
        
        // Handle Category Input
        const categoryInput = document.getElementById('category_input');
        const categoryIdField = document.getElementById('category_id');
        const datalist = document.getElementById('categories_list');

        categoryInput.addEventListener('input', function() {
            const val = this.value;
            const option = Array.from(datalist.options).find(opt => opt.value === val);
            if (option) {
                categoryIdField.value = option.getAttribute('data-id');
            } else {
                categoryIdField.value = ''; 
            }
        });
    });

    function addIngredient(selectedId = null, quantity = null) {
        const container = document.getElementById('ingredients-container');
        
        const div = document.createElement('div');
        div.className = 'ingredient-item';
        div.innerHTML = `
            <div class="form-group" style="margin-bottom: 0;">
                <label>المكون</label>
                <select name="ingredients[]" class="ingredient-select" onchange="calculateNutrition()" required>
                    <option value="">اختر المكون</option>
                    ${ingredientsData.map(ing => `<option value="${ing.ingredients_id}" ${selectedId == ing.ingredients_id ? 'selected' : ''}>${ing.name_ar} (${ing.calories} kcal/100g)</option>`).join('')}
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>الكمية (غ)</label>
                <input type="number" name="ingredient_quantities[]" step="0.01" placeholder="0" value="${quantity || ''}" oninput="calculateNutrition()" required>
            </div>
            <button type="button" class="remove-ingredient-btn" onclick="removeIngredient(this)">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(div);
        
        // Calculate initially if loading existing data
        if (selectedId) calculateNutrition();
    }

    function removeIngredient(btn) {
        const container = document.getElementById('ingredients-container');
        if (container.children.length > 1) {
            btn.closest('.ingredient-item').remove();
            calculateNutrition();
        } else {
            alert('مكون واحد على الأقل مطلوب.');
        }
    }

    function calculateNutrition() {
        let totalCalories = 0;
        let totalProtein = 0;
        let totalFat = 0;
        let totalCarbs = 0;
        let totalQuantity = 0;

        const rows = document.querySelectorAll('.ingredient-item');
        
        rows.forEach(row => {
            const select = row.querySelector('.ingredient-select');
            const qtyInput = row.querySelector('input[type="number"]');
            
            const ingredientId = select.value;
            const quantity = parseFloat(qtyInput.value) || 0;
            
            if (ingredientId && quantity > 0) {
                const ingredient = ingredientsData.find(i => i.ingredients_id == ingredientId);
                if (ingredient) {
                    const ratio = quantity / 100;
                    
                    totalCalories += (parseFloat(ingredient.calories) || 0) * ratio;
                    totalProtein += (parseFloat(ingredient.protein_g) || 0) * ratio;
                    totalFat += (parseFloat(ingredient.fat_g) || 0) * ratio;
                    totalCarbs += (parseFloat(ingredient.carbs_g) || 0) * ratio;
                    totalQuantity += quantity;
                }
            }
        });

        document.getElementById('calories').value = totalCalories.toFixed(2);
        document.getElementById('protein_g').value = totalProtein.toFixed(2);
        document.getElementById('fat_g').value = totalFat.toFixed(2);
        document.getElementById('carbs_g').value = totalCarbs.toFixed(2);
        document.getElementById('quantity_g').value = totalQuantity.toFixed(2);
    }

    // Modal Logic
    const modal = document.getElementById('categoryModal');
    
    function openModal() {
        modal.style.display = 'block';
        document.getElementById('new_category_name').focus();
    }
    
    function closeModal() {
        modal.style.display = 'none';
        document.getElementById('new_category_name').value = '';
        document.getElementById('category-error').style.display = 'none';
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    function saveCategory() {
        const name = document.getElementById('new_category_name').value;
        const errorDiv = document.getElementById('category-error');
        
        if (!name) {
            errorDiv.textContent = 'اسم الفئة مطلوب';
            errorDiv.style.display = 'block';
            return;
        }

        fetch('{{ route("admin.categories.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ category_name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add to datalist
                const datalist = document.getElementById('categories_list');
                const option = document.createElement('option');
                option.value = data.category.category_name;
                option.setAttribute('data-id', data.category.category_id);
                datalist.appendChild(option);
                
                // Select it
                document.getElementById('category_input').value = data.category.category_name;
                document.getElementById('category_id').value = data.category.category_id;
                
                closeModal();
            } else {
                errorDiv.textContent = data.message || 'خطأ في إنشاء الفئة';
                errorDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorDiv.textContent = 'حدث خطأ ما';
            errorDiv.style.display = 'block';
        });
    }
</script>
@endpush
@endsection
