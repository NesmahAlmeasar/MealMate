@extends('layouts.admin_app')

@section('title', 'إدارة المكونات الغذائية')

@section('content')
    <div style="padding: var(--spacing-xl);">
        <div class="page-header">
            <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; color: var(--text-dark);" data-i18n="ingredientsManagement">إدارة المكونات الغذائية</h1>
            <button class="btn btn-primary" id="openAddIngredientModal">
                <i class="fas fa-plus"></i>
                <span data-i18n="addNewIngredient">أضف مكون جديد</span>
            </button>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>خطأ!</strong> يرجى التحقق من البيانات:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card" style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: var(--spacing-md); text-align: right; border-bottom: 2px solid var(--border-color);">الاسم (عربي)</th>
                        <th style="padding: var(--spacing-md); text-align: right; border-bottom: 2px solid var(--border-color);">المصطلح (USDA)</th>
                        <th style="padding: var(--spacing-md); text-align: right; border-bottom: 2px solid var(--border-color);">السعرات</th>
                        <th style="padding: var(--spacing-md); text-align: center; border-bottom: 2px solid var(--border-color);">نباتي</th>
                        <th style="padding: var(--spacing-md); text-align: center; border-bottom: 2px solid var(--border-color);">جلوتين</th>
                        <th style="padding: var(--spacing-md); text-align: center; border-bottom: 2px solid var(--border-color);">ألبان</th>
                        <th style="padding: var(--spacing-md); text-align: center; border-bottom: 2px solid var(--border-color);">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($ingredients->isNotEmpty())
                        @foreach ($ingredients as $ingredient)
                        <tr>
                            <td style="padding: var(--spacing-md); border-bottom: 1px solid var(--bg-gray-dark);">{{ $ingredient->name_ar }}</td>
                            <td style="padding: var(--spacing-md); border-bottom: 1px solid var(--bg-gray-dark);">{{ $ingredient->usda_term ?? '-' }}</td>
                            <td style="padding: var(--spacing-md); border-bottom: 1px solid var(--bg-gray-dark);">{{ $ingredient->calories }}</td>
                            <td style="padding: var(--spacing-md); text-align: center; border-bottom: 1px solid var(--bg-gray-dark);">
                                @if($ingredient->is_vegan) 
                                    <span style="color: var(--green-success); font-size: 1.2em;">✔</span> 
                                @else 
                                    <span style="color: var(--red-accent); font-size: 1.2em;">✘</span> 
                                @endif
                            </td>
                            <td style="padding: var(--spacing-md); text-align: center; border-bottom: 1px solid var(--bg-gray-dark);">
                                @if($ingredient->has_gluten) 
                                    <span style="color: var(--red-accent); font-size: 1.2em;">✔</span> 
                                @else 
                                    <span style="color: var(--green-success); font-size: 1.2em;">✘</span> 
                                @endif
                            </td>
                            <td style="padding: var(--spacing-md); text-align: center; border-bottom: 1px solid var(--bg-gray-dark);">
                                @if($ingredient->has_dairy) 
                                    <span style="color: var(--red-accent); font-size: 1.2em;">✔</span> 
                                @else 
                                    <span style="color: var(--green-success); font-size: 1.2em;">✘</span> 
                                @endif
                            </td>
                            <td style="padding: var(--spacing-md); text-align: center; border-bottom: 1px solid var(--bg-gray-dark);">
                                <div class="action-buttons" style="justify-content: center;">
                                    @php
                                        $ingredientData = [
                                            "id" => $ingredient->ingredients_id,
                                            "name_ar" => $ingredient->name_ar,
                                            "usda_term" => $ingredient->usda_term,
                                            "calories" => $ingredient->calories,
                                            "protein_g" => $ingredient->protein_g,
                                            "fat_g" => $ingredient->fat_g,
                                            "carbs_g" => $ingredient->carbs_g,
                                            "is_vegan" => $ingredient->is_vegan,
                                            "has_gluten" => $ingredient->has_gluten,
                                            "has_dairy" => $ingredient->has_dairy,
                                        ];
                                    @endphp

                                    <a href="#" 
                                       class="action-btn edit edit-btn" 
                                       title="تعديل المكون" 
                                       data-ingredient-id="{{ $ingredient->ingredients_id }}"
                                       data-ingredient-data="{{ json_encode($ingredientData) }}">
                                        ✏️
                                    </a>
                                    <button class="action-btn delete delete-btn" title="حذف المكون" data-ingredient-id="{{ $ingredient->ingredients_id }}">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="7" style="text-align: center; padding: 20px; color: var(--text-light);">لم يتم العثور على مكونات.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $ingredients->links('vendor.pagination.custom') }}
        </div>
    </div>

    {{-- Modal (Add/Edit) --}}
    <div id="addIngredientModal" class="modal" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="modal-content">
            
            <div class="modal-header">
                <h2 class="modal-title">إضافة مكون جديد</h2>
                <span class="close-btn" id="closeAddIngredientModal">&times;</span>
            </div>

            <form action="{{ route('nutrition-manager.ingredients.store') }}" method="POST">
                @csrf
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name_ar">الاسم الشائع  <span style="color: red;">*</span></label>
                        <input type="text" id="name_ar" name="name_ar" class="form-control" required>
                        @error('name_ar')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="usda_term">المصطلح (USDA)</label>
                        <input type="text" id="usda_term" name="usda_term" class="form-control">
                        @error('usda_term')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="calories">السعرات الحرارية</label>
                            <input type="number" step="0.01" id="calories" name="calories" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="protein_g">البروتين (جم)</label>
                            <input type="number" step="0.01" id="protein_g" name="protein_g" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="fat_g">الدهون (جم)</label>
                            <input type="number" step="0.01" id="fat_g" name="fat_g" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="carbs_g">الكربوهيدرات (جم)</label>
                            <input type="number" step="0.01" id="carbs_g" name="carbs_g" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>الخصائص</label>
                        <div style="display: flex; gap: 20px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="is_vegan" value="1" id="is_vegan">
                                <span>نباتي</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="has_gluten" value="1" id="has_gluten">
                                <span>يحتوي على جلوتين</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="has_dairy" value="1" id="has_dairy">
                                <span>يحتوي على ألبان</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="cancelAddIngredientModal">إلغاء</button>
                    <button type="submit" class="save-btn">حفظ المكون</button>
                </div>
            </form>

        </div>
    </div>

    {{-- Hidden Delete Form --}}
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE') 
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            const modal = document.getElementById('addIngredientModal');
            const openBtn = document.getElementById('openAddIngredientModal');
            const closeBtn = document.getElementById('closeAddIngredientModal');
            const cancelBtn = document.getElementById('cancelAddIngredientModal');
            const form = modal ? modal.querySelector('form') : null;
            const modalTitle = modal ? modal.querySelector('.modal-title') : null;

            function openModal() { if (modal) { modal.style.display = 'flex'; } }
            function closeModal() { if (modal) { modal.style.display = 'none'; } }
            
            // Open Modal for Add
            if(openBtn) {
                openBtn.addEventListener('click', function() {
                    if (form) {
                        form.action = '{{ route('nutrition-manager.ingredients.store') }}';
                        form.method = 'POST';
                        
                        // Remove _method field if exists
                        const methodField = form.querySelector('input[name="_method"]');
                        if (methodField) methodField.remove();
                        
                        form.reset(); 
                        
                        // Clear fields manually
                        form.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => input.value = '');
                        form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => checkbox.checked = false);
                        
                        if (modalTitle) modalTitle.textContent = 'إضافة مكون جديد';
                    }
                    openModal();
                });
            }

            if(closeBtn) closeBtn.addEventListener('click', closeModal);
            if(cancelBtn) cancelBtn.addEventListener('click', closeModal);

            if (modal) {
                modal.addEventListener('click', function(event) {
                    if (event.target === modal) { closeModal(); }
                });
            }

            // Edit Logic
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    try {
                        const ingredientData = JSON.parse(this.getAttribute('data-ingredient-data'));
                        
                        if (modalTitle) modalTitle.textContent = 'تعديل المكون: ' + ingredientData.name_ar;
                        
                        if (form) {
                            form.action = '{{ url('nutrition-manager/ingredients') }}/' + ingredientData.id;
                            
                            let methodField = form.querySelector('input[name="_method"]');
                            if (!methodField) {
                                methodField = document.createElement('input');
                                methodField.setAttribute('type', 'hidden');
                                methodField.setAttribute('name', '_method');
                                form.appendChild(methodField);
                            }
                            methodField.value = 'PUT';
                            
                            if(form.querySelector('#name_ar')) form.querySelector('#name_ar').value = ingredientData.name_ar || '';
                            if(form.querySelector('#usda_term')) form.querySelector('#usda_term').value = ingredientData.usda_term || '';
                            if(form.querySelector('#calories')) form.querySelector('#calories').value = ingredientData.calories || '';
                            if(form.querySelector('#protein_g')) form.querySelector('#protein_g').value = ingredientData.protein_g || '';
                            if(form.querySelector('#fat_g')) form.querySelector('#fat_g').value = ingredientData.fat_g || '';
                            if(form.querySelector('#carbs_g')) form.querySelector('#carbs_g').value = ingredientData.carbs_g || '';
                            
                            if(form.querySelector('#is_vegan')) form.querySelector('#is_vegan').checked = ingredientData.is_vegan == 1;
                            if(form.querySelector('#has_gluten')) form.querySelector('#has_gluten').checked = ingredientData.has_gluten == 1;
                            if(form.querySelector('#has_dairy')) form.querySelector('#has_dairy').checked = ingredientData.has_dairy == 1;
                            
                            openModal();
                        }
                    } catch (error) {
                        console.error('Error parsing ingredient data:', error);
                        alert('خطأ في تحميل البيانات.');
                    }
                });
            });

            // Delete Logic
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const ingredientId = this.getAttribute('data-ingredient-id');
                    if(confirm('هل أنت متأكد أنك تريد حذف هذا المكون؟')) {
                        const deleteForm = document.getElementById('delete-form');
                        deleteForm.action = '{{ url('nutrition-manager/ingredients') }}/' + ingredientId;
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
