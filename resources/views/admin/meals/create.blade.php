@extends('layouts.admin_app')

@section('title', isset($restaurant) ? 'إضافة وجبة إلى ' . $restaurant->name : 'إضافة وجبة جديدة')

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
</style>
@endpush

@section('content')
<div class="diet-form">
    <div class="form-header">
        <h1>{{ isset($restaurant) ? 'إضافة وجبة إلى ' . $restaurant->name : 'إضافة وجبة جديدة' }}</h1>
        <p>املأ التفاصيل أدناه. سيتم حساب القيم الغذائية تلقائياً بناءً على المكونات.</p>
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

    <form action="{{ isset($restaurant) ? route('admin.restaurants.meals.store', $restaurant->restaurants_id) : route('admin.meals.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-grid">
            <!-- Meal Name -->
            <div class="form-group">
                <label for="name">اسم الوجبة *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="مثال: سلطة دجاج مشوي">
                @error('name') <span class="error-message">{{ $message }}</span> @enderror
            </div>

                <!-- Description -->
            <div class="form-group full-width">
                <label for="description">الوصف</label>
                <textarea id="description" name="description" rows="3" placeholder="وصف الوجبة...">{{ old('description') }}</textarea>
            </div>


            <!-- Restaurant Selection (if not pre-selected) -->
            @if(!isset($restaurant))
            <div class="form-group">
                <label for="restaurant_id">المطعم</label>
                <select id="restaurant_id" name="restaurant_id" required>
                    <option value="">اختر المطعم </option>
                    @foreach($restaurants as $resto)
                        <option value="{{ $resto->restaurants_id }}" {{ old('restaurant_id') == $resto->restaurants_id ? 'selected' : '' }}>
                            {{ $resto->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Category with Add New Button -->
            <div class="form-group">
                <label for="category_input">الفئة</label>
                <div class="input-group">
                    <input list="categories_list" id="category_input" class="form-control" placeholder="اكتب للبحث أو إضافة جديد..."  required>
                    <datalist id="categories_list">
                        @foreach($categories as $category)
                            <option value="{{ $category->category_name }}" data-id="{{ $category->category_id }}">
                        @endforeach
                    </datalist>
                    <input type="hidden" name="category_id" id="category_id" value="{{ old('category_id') }}">
                    <button type="button" class="btn-add-category" onclick="openModal()">
                        <i class="fas fa-plus"></i> جديد
                    </button>
                </div>
                @error('category_id') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Price -->
            <div class="form-group">
                <label for="price">السعر ($) *</label>
                <input type="number" id="price" name="price" step="0.01" value="{{ old('price') }}" required placeholder="0.00">
                @error('price') <span class="error-message">{{ $message }}</span> @enderror
            </div>



            <!-- Preparation Time -->
            <div class="form-group">
                <label for="preparation_time">وقت التحضير (دقائق)</label>
                <input type="number" id="preparation_time" name="preparation_time" min="1" value="{{ old('preparation_time') }}" placeholder="مثال: 30" required>
            </div>

           
            <!-- Photo -->
            <div class="form-group">
                <label for="photo" class="form-label">صورة الوجبة</label>
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                @error('photo') <span class="error-message">{{ $message }}</span> @enderror
            </div>

         <!-- Ingredients Section -->
            <div class="ingredients-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0;">المكونات</h3>
                        <p style="font-size: 13px; color: #666; margin: 5px 0 0 0;">أضف المكونات يدوياً أو استخدم الذكاء الاصطناعي لاقتراحها تلقائياً</p>
                    </div>
                    <button type="button" onclick="suggestIngredientsAI()" 
                            class="btn-ai-suggest"
                            style="background: rgba(107, 142, 35, 0.15); 
                       color: #556B2F; 
                       border: 2px solid #6B8E23; 
                       padding: 10px 20px; 
                       border-radius: 8px; 
                       cursor: pointer; 
                       font-weight: 700;
                       display: flex;
                       align-items: center;
                       gap: 8px;
                       box-shadow: 0 4px 15px rgba(107, 142, 35, 0.2);
                       transition: all 0.3s ease;">
                        <i class="fas fa-magic"></i> اقتراح الذكاء الاصطناعي
                    </button>
                </div>
                
                <!-- Loading Indicator -->
                <div id="ai-loading" style="display: none; text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 15px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #667eea;"></i>
                    <p style="margin: 10px 0 0 0; color: #666;">الذكاء الاصطناعي يقوم بتحليل وجبتك...</p>
                </div>
                
                <div id="ingredients-container">
                    <!-- Template for JS -->
                </div>
                
                <button type="button" class="add-ingredient-btn" onclick="addIngredient()">
                    <i class="fas fa-plus"></i> إضافة مكون يدوياً
                </button>
            </div>
        


                <!-- Quantity -->
            <div class="form-group">
                <label for="quantity_g">الكمية الكلية (غ)</label>
                <input type="number" id="quantity_g" name="quantity_g" step="0.01" value="{{ old('quantity_g') }}" class="calculated-input" readonly>
                <small style="color: #666;">يتم حسابها تلقائياً من المكونات</small>
            </div>


            <!-- Nutritional Info (Calculated) -->
            <div class="form-group full-width">
                <h3 style="margin-bottom: 15px; color: #333; font-size: 18px;">المعلومات الغذائية (محسوبة تلقائيا حسب المكونات )</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="calories">سعرات حرارية</label>
                        <input type="number" id="calories" name="calories" step="0.01" value="{{ old('calories') }}" class="calculated-input" readonly>
                    </div>
                    <div class="form-group">
                        <label for="protein_g">بروتين (غ)</label>
                        <input type="number" id="protein_g" name="protein_g" step="0.01" value="{{ old('protein_g') }}" class="calculated-input" readonly>
                    </div>
                    <div class="form-group">
                        <label for="fat_g">دهون (غ)</label>
                        <input type="number" id="fat_g" name="fat_g" step="0.01" value="{{ old('fat_g') }}" class="calculated-input" readonly>
                    </div>
                    <div class="form-group">
                        <label for="carbs_g">كربوهيدرات (غ)</label>
                        <input type="number" id="carbs_g" name="carbs_g" step="0.01" value="{{ old('carbs_g') }}" class="calculated-input" readonly>
                    </div>
                </div>
            </div>

            
           
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">حفظ الوجبة</button>
            <a href="{{ isset($restaurant) ? route('admin.restaurants.show', $restaurant->restaurants_id) : route('admin.meals.index') }}" class="btn-cancel">إلغاء</a>
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
    
    // Initial Setup
    document.addEventListener('DOMContentLoaded', function() {
        // Add one empty ingredient row if none exist (or restore old input)
        addIngredient(); 
        
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
                categoryIdField.value = ''; // Reset if not found (or handle new category creation on submit if backend supports it)
            }
        });
    });

    function addIngredient() {
        const container = document.getElementById('ingredients-container');
        const index = container.children.length;
        
        const div = document.createElement('div');
        div.className = 'ingredient-item';
        div.innerHTML = `
            <div class="form-group" style="margin-bottom: 0;">
                <label>Ingredient</label>
                <select name="ingredients[]" class="ingredient-select" onchange="calculateNutrition()" required>
                    <option value="">Select Ingredient</option>
                    ${ingredientsData.map(ing => `<option value="${ing.ingredients_id}">${ing.name_ar} (${ing.calories} kcal/100g)</option>`).join('')}
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Quantity (g)</label>
                <input type="number" name="ingredient_quantities[]" step="0.01" placeholder="0" oninput="calculateNutrition()" required>
            </div>
            <button type="button" class="remove-ingredient-btn" onclick="removeIngredient(this)">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(div);
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

    // ========== AI Suggest Functions ==========
    async function suggestIngredientsAI() {
        const mealName = document.getElementById('name').value;
        const description = document.getElementById('description').value;
        const quantity = document.getElementById('quantity_g').value;
        
        if (!mealName) {
            alert('يرجى إدخال اسم الوجبة أولاً');
            return;
        }
        
        // Show loading
        document.getElementById('ai-loading').style.display = 'block';
        document.getElementById('ingredients-container').innerHTML = '';
        
        try {
            const response = await fetch('{{ route("admin.meals.ai_suggest") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: mealName,
                    description: description,
                    quantity: quantity
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Clear existing ingredients
                document.getElementById('ingredients-container').innerHTML = '';
                
                // Add suggested ingredients
                result.data.forEach(item => {
                    addIngredientWithData(item.ingredients_id, item.quantity_g);
                });
                
                // Calculate nutrition
                calculateNutrition();
                
                alert('✨ تمت إضافة اقتراحات الذكاء الاصطناعي بنجاح!');
            } else {
                alert('فشل الحصول على اقتراحات: ' + (result.message || 'خطأ غير معروف'));
                // Add one empty ingredient as fallback
                addIngredient();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('خطأ في الاتصال بخدمة الذكاء الاصطناعي');
            // Add one empty ingredient as fallback
            addIngredient();
        } finally {
            document.getElementById('ai-loading').style.display = 'none';
        }
    }

    function addIngredientWithData(ingredientId, quantity) {
        const container = document.getElementById('ingredients-container');
        const div = document.createElement('div');
        div.className = 'ingredient-item';
        div.innerHTML = `
            <div class="form-group" style="margin-bottom: 0;">
                <label>Ingredient</label>
                <select name="ingredients[]" class="ingredient-select" onchange="calculateNutrition()" required>
                    <option value="">Select Ingredient</option>
                    ${ingredientsData.map(ing => 
                        `<option value="${ing.ingredients_id}" ${ing.ingredients_id == ingredientId ? 'selected' : ''}>
                            ${ing.name_ar} (${ing.calories} kcal/100g)
                        </option>`
                    ).join('')}
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Quantity (g)</label>
                <input type="number" name="ingredient_quantities[]" step="0.01" value="${quantity}" oninput="calculateNutrition()" required>
            </div>
            <button type="button" class="remove-ingredient-btn" onclick="removeIngredient(this)">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(div);
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
                    // Values are usually per 100g or per unit. Assuming per 100g based on common practice and DB schema comments if any.
                    // If DB values are per 1g, remove /100. Assuming per 100g.
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
