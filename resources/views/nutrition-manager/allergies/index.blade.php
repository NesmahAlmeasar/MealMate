@extends('layouts.admin_app')

@section('title', 'إدارة الحساسية')

@section('content')
    <div style="padding: var(--spacing-xl);">
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl);">
            <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; color: var(--text-dark); margin: 0;" data-i18n="allergiesManagement">إدارة الحساسية</h1>
            <button class="btn btn-primary" id="openAddAllergyModal">
                <i class="fas fa-plus"></i>
                <span data-i18n="addNewAllergy">أضف حساسية جديدة</span>
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
                        <th style="padding: var(--spacing-md); text-align: right; border-bottom: 2px solid var(--border-color); width: 30%;">اسم الحساسية</th>
                        <th style="padding: var(--spacing-md); text-align: right; border-bottom: 2px solid var(--border-color); width: 50%;">المكونات المرتبطة</th>
                        <th style="padding: var(--spacing-md); text-align: center; border-bottom: 2px solid var(--border-color);">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($allergies->isNotEmpty())
                        @foreach ($allergies as $allergy)
                        <tr>
                            <td style="padding: var(--spacing-md); border-bottom: 1px solid var(--bg-gray-dark);">{{ $allergy->allergies }}</td>
                            <td style="padding: var(--spacing-md); border-bottom: 1px solid var(--bg-gray-dark);">
                                @if($allergy->ingredients->isNotEmpty())
                                    @foreach($allergy->ingredients as $in)
                                        <span class="badge badge-info" style="margin-right: 4px; margin-bottom: 4px;">{{ $in->name_ar }}</span>
                                    @endforeach
                                @else
                                    <span style="color: var(--text-light);">لا توجد مكونات مرتبطة</span>
                                @endif
                            </td>
                            <td style="padding: var(--spacing-md); text-align: center; border-bottom: 1px solid var(--bg-gray-dark);">
                                <div class="action-buttons" style="justify-content: center;">
                                    @php
                                        $allergyData = [
                                            "id" => $allergy->allergies_id,
                                            "allergies" => $allergy->allergies,
                                            "ingredient_ids" => $allergy->ingredients->pluck('ingredients_id')->toArray(),
                                        ];
                                    @endphp

                                    <a href="#" 
                                       class="action-btn edit edit-btn" 
                                       title="تعديل الحساسية" 
                                       data-allergy-id="{{ $allergy->allergies_id }}"
                                       data-allergy-data="{{ json_encode($allergyData) }}">
                                        ✏️
                                    </a>
                                    <button class="action-btn delete delete-btn" title="حذف الحساسية" data-allergy-id="{{ $allergy->allergies_id }}">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="3" style="text-align: center; padding: 20px; color: var(--text-light);">لم يتم العثور على حساسية.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $allergies->links('vendor.pagination.custom') }}
        </div>
    </div>

    {{-- Modal (Add/Edit) --}}
    <div id="addAllergyModal" class="modal" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="modal-content">
            
            <div class="modal-header">
                <h2 class="modal-title">إضافة حساسية جديدة</h2>
                <span class="close-btn" id="closeAddAllergyModal">&times;</span>
            </div>

            <form action="{{ route('nutrition-manager.allergies.store') }}" method="POST">
                @csrf
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="allergies">اسم الحساسية <span style="color: var(--red-accent);">*</span></label>
                        <input type="text" id="allergies" name="allergies" required>
                        @error('allergies')
                            <span style="color: var(--red-accent); font-size: 0.85em;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>المكونات المرتبطة</label>
                        <div style="max-height: 250px; overflow-y: auto; border: 1px solid var(--border-color); padding: var(--spacing-md); border-radius: var(--radius-md); background: var(--bg-gray);">
                            @if(isset($ingredients) && $ingredients->isNotEmpty())
                                @foreach($ingredients as $ingredient)
                                    <div style="margin-bottom: 8px;">
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                            <input type="checkbox" name="ingredients[]" value="{{ $ingredient->ingredients_id }}" class="ingredient-checkbox" style="width: auto;">
                                            <span>{{ $ingredient->name_ar }}</span>
                                            @if($ingredient->usda_term) 
                                                <small style="color: var(--text-light);">({{ $ingredient->usda_term }})</small>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <p style="color: var(--text-light); text-align: center;">لا توجد مكونات متاحة</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelAddAllergyModal">إلغاء</button>
                    <button type="submit" class="btn btn-primary save-btn">حفظ الحساسية</button>
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
            
            const modal = document.getElementById('addAllergyModal');
            const openBtn = document.getElementById('openAddAllergyModal');
            const closeBtn = document.getElementById('closeAddAllergyModal');
            const cancelBtn = document.getElementById('cancelAddAllergyModal');
            const form = modal ? modal.querySelector('form') : null;
            const modalTitle = modal ? modal.querySelector('.modal-title') : null;

            function openModal() { if (modal) { modal.style.display = 'flex'; } }
            function closeModal() { if (modal) { modal.style.display = 'none'; } }
            
            // Open Modal for Add
            if(openBtn) {
                openBtn.addEventListener('click', function() {
                    if (form) {
                        form.action = '{{ route('nutrition-manager.allergies.store') }}';
                        form.method = 'POST';
                        
                        // Remove _method field if exists
                        const methodField = form.querySelector('input[name="_method"]');
                        if (methodField) methodField.remove();
                        
                        form.reset(); 
                        
                        // Clear fields manually
                        form.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
                        form.querySelectorAll('.ingredient-checkbox').forEach(checkbox => checkbox.checked = false);
                        
                        if (modalTitle) modalTitle.textContent = 'إضافة حساسية جديدة';
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
                        const allergyData = JSON.parse(this.getAttribute('data-allergy-data'));
                        
                        if (modalTitle) modalTitle.textContent = 'تعديل الحساسية: ' + allergyData.allergies;
                        
                        if (form) {
                            form.action = '{{ url('nutrition-manager/allergies') }}/' + allergyData.id;
                            
                            let methodField = form.querySelector('input[name="_method"]');
                            if (!methodField) {
                                methodField = document.createElement('input');
                                methodField.setAttribute('type', 'hidden');
                                methodField.setAttribute('name', '_method');
                                form.appendChild(methodField);
                            }
                            methodField.value = 'PUT';
                            
                            if(form.querySelector('#allergies')) form.querySelector('#allergies').value = allergyData.allergies || '';
                            
                            // Handle ingredient checkboxes
                            form.querySelectorAll('.ingredient-checkbox').forEach(checkbox => {
                                checkbox.checked = false;
                            });
                            
                            if (allergyData.ingredient_ids && Array.isArray(allergyData.ingredient_ids)) {
                                allergyData.ingredient_ids.forEach(ingredientId => {
                                    const checkbox = form.querySelector(`.ingredient-checkbox[value="${ingredientId}"]`);
                                    if (checkbox) {
                                        checkbox.checked = true;
                                    }
                                });
                            }
                            
                            openModal();
                        }
                    } catch (error) {
                        console.error('Error parsing allergy data:', error);
                        alert('خطأ في تحميل البيانات.');
                    }
                });
            });

            // Delete Logic
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const allergyId = this.getAttribute('data-allergy-id');
                    if(confirm('هل أنت متأكد أنك تريد حذف هذه الحساسية؟')) {
                        const deleteForm = document.getElementById('delete-form');
                        deleteForm.action = '{{ url('nutrition-manager/allergies') }}/' + allergyId;
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
