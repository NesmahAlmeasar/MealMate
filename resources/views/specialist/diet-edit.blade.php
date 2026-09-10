@extends('layouts.admin_app')

@section('title', 'تعديل الحمية')

@push('styles')
<style>
    .form-container {
        max-width: 1200px;
        margin: 20px auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .form-header {
        margin-bottom: 30px;
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
        margin-bottom: 8px;
        font-weight: bold;
        color: #555;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .checkbox-group input[type="checkbox"] {
        width: auto;
    }
    .meals-selection {
        grid-column: 1 / -1;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        background: #f8f9fa;
    }
    .meals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
    }
    .meal-checkbox-item {
        background: white;
        padding: 15px;
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        cursor: pointer;
        transition: all 0.3s;
    }
    .meal-checkbox-item:hover {
        border-color: #667eea;
    }
    .meal-checkbox-item.selected {
        border-color: #667eea;
        background: #f0f4ff;
    }
    .restrictions-section {
        grid-column: 1 / -1;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        background: #f8f9fa;
    }
    .restriction-item {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 10px;
        margin-bottom: 10px;
        align-items: end;
    }
    .add-restriction-btn {
        background: #28a745;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
    }
    .remove-restriction-btn {
        background: #dc3545;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }
    .btn-submit {
        background: rgba(107, 142, 35, 0.15);
        color: #556B2F;
        border: 2px solid #6B8E23;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
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
        background: #6c757d;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
    }
    .current-image {
        max-width: 200px;
        border-radius: 8px;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <h1>تعديل الحمية: {{ $diet->name }}</h1>
        <p>قم بتحديث تفاصيل الحمية أدناه</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('specialist.diets.update', $diet->diets_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <div class="form-group">
                <label for="name">اسم الحمية *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $diet->name) }}" required>
            </div>

            <div class="form-group">
                <label for="photo">الصورة (اتركها فارغة للاحتفاظ بالحالية)</label>
                <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
                @if($diet->photo_url)
                    <img src="{{ asset('storage/' . $diet->photo_url) }}" alt="Current photo" class="current-image">
                @endif
            </div>

            <div class="form-group full-width">
                <label for="description">الوصف</label>
                <textarea id="description" name="description">{{ old('description', $diet->description) }}</textarea>
            </div>

            <div class="form-group full-width">
                <label for="warning">تحذيرات</label>
                <textarea id="warning" name="warning" placeholder="أي تحذيرات أو احتياطات لهذه الحمية">{{ old('warning', $diet->warning) }}</textarea>
            </div>

            <div class="form-group full-width">
                <label for="advice">نصائح</label>
                <textarea id="advice" name="advice" placeholder="نصائح لاتباع هذه الحمية">{{ old('advice', $diet->advice) }}</textarea>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public', $diet->is_public) ? 'checked' : '' }}>
                    <label for="is_public" style="margin-bottom: 0;">جعل هذه الحمية عامة</label>
                </div>
            </div>

            <div class="meals-selection">
                <h3>اختر وجبات لهذه الحمية</h3>
                <p style="color: #666; margin-bottom: 15px;">اختر الوجبات التي تشكل جزءاً من خطة الحمية هذه</p>
                <div class="meals-grid">
                    @php
                        $selectedMealIds = $diet->meals->pluck('meals_id')->toArray();
                    @endphp
                    @foreach($meals as $meal)
                        <div class="meal-checkbox-item {{ in_array($meal->meals_id, $selectedMealIds) ? 'selected' : '' }}" onclick="toggleMealCheckbox({{ $meal->meals_id }})">
                            <input type="checkbox" 
                                   id="meal_{{ $meal->meals_id }}" 
                                   name="meals[]" 
                                   value="{{ $meal->meals_id }}"
                                   {{ in_array($meal->meals_id, $selectedMealIds) ? 'checked' : '' }}
                                   style="display: none;">
                            <label for="meal_{{ $meal->meals_id }}" style="cursor: pointer; margin: 0;">
                                <strong>{{ $meal->name }}</strong><br>
                                <small style="color: #666;">
                                    {{ $meal->category->category_name ?? 'N/A' }} | 
                                    {{ $meal->calories ?? 0 }} kcal
                                </small>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="restrictions-section">
                <h3>القيود الغذائية</h3>
                <p style="color: #666; margin-bottom: 15px;">تحديث القيود لهذه الحمية</p>
                <div id="restrictions-container">
                    @forelse($diet->restrictions as $index => $restriction)
                        <div class="restriction-item">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>الوصف</label>
                                <input type="text" name="restrictions[{{ $index }}][restriction]" value="{{ $restriction->restriction }}" placeholder="مثال: الحد الأقصى للسعرات">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>الحقل</label>
                                <select name="restrictions[{{ $index }}][field_name]">
                                    <option value="">اختر الحقل</option>
                                    <option value="calories" {{ $restriction->field_name == 'calories' ? 'selected' : '' }}>سعرات</option>
                                    <option value="protein_g" {{ $restriction->field_name == 'protein_g' ? 'selected' : '' }}>بروتين (جم)</option>
                                    <option value="carbs_g" {{ $restriction->field_name == 'carbs_g' ? 'selected' : '' }}>كربوهيدرات (جم)</option>
                                    <option value="fat_g" {{ $restriction->field_name == 'fat_g' ? 'selected' : '' }}>دهون (جم)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>العملية</label>
                                <select name="restrictions[{{ $index }}][operator]">
                                    <option value="<" {{ $restriction->operator == '<' ? 'selected' : '' }}>أقل من (<)</option>
                                    <option value="<=" {{ $restriction->operator == '<=' ? 'selected' : '' }}>أقل أو يساوي (<=)</option>
                                    <option value="=" {{ $restriction->operator == '=' ? 'selected' : '' }}>يساوي (=)</option>
                                    <option value=">=" {{ $restriction->operator == '>=' ? 'selected' : '' }}>أكبر أو يساوي (>=)</option>
                                    <option value=">" {{ $restriction->operator == '>' ? 'selected' : '' }}>أكبر من (>)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>القيمة</label>
                                <input type="text" name="restrictions[{{ $index }}][value]" value="{{ $restriction->value }}" placeholder="مثال: 2000">
                            </div>
                            <button type="button" class="remove-restriction-btn" onclick="removeRestriction(this)">حذف</button>
                        </div>
                    @empty
                        <div class="restriction-item">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>الوصف</label>
                                <input type="text" name="restrictions[0][restriction]" placeholder="مثال: الحد الأقصى للسعرات">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>الحقل</label>
                                <select name="restrictions[0][field_name]">
                                    <option value="">اختر الحقل</option>
                                    <option value="calories">سعرات</option>
                                    <option value="protein_g">بروتين (جم)</option>
                                    <option value="carbs_g">كربوهيدرات (جم)</option>
                                    <option value="fat_g">دهون (جم)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>العملية</label>
                                <select name="restrictions[0][operator]">
                                    <option value="<">أقل من (<)</option>
                                    <option value="<=">أقل أو يساوي (<=)</option>
                                    <option value="=">يساوي (=)</option>
                                    <option value=">=">أكبر أو يساوي (>=)</option>
                                    <option value=">">أكبر من (>)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>القيمة</label>
                                <input type="text" name="restrictions[0][value]" placeholder="مثال: 2000">
                            </div>
                            <button type="button" class="remove-restriction-btn" onclick="removeRestriction(this)">حذف</button>
                        </div>
                    @endforelse
                </div>
                <button type="button" class="add-restriction-btn" onclick="addRestriction()">+ إضافة قيد</button>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">تحديث الحمية</button>
            <a href="{{ route('specialist.diets.index') }}" class="btn-cancel">إلغاء</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let restrictionCount = {{ $diet->restrictions->count() > 0 ? $diet->restrictions->count() : 1 }};

    function toggleMealCheckbox(mealId) {
        const checkbox = document.getElementById('meal_' + mealId);
        const card = checkbox.closest('.meal-checkbox-item');
        
        checkbox.checked = !checkbox.checked;
        
        if (checkbox.checked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    }

    function addRestriction() {
        const container = document.getElementById('restrictions-container');
        const newRestriction = `
            <div class="restriction-item">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>الوصف</label>
                    <input type="text" name="restrictions[${restrictionCount}][restriction]" placeholder="مثال: الحد الأقصى للسعرات">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>الحقل</label>
                    <select name="restrictions[${restrictionCount}][field_name]">
                        <option value="">اختر الحقل</option>
                        <option value="calories">سعرات</option>
                        <option value="protein_g">بروتين (جم)</option>
                        <option value="carbs_g">كربوهيدرات (جم)</option>
                        <option value="fat_g">دهون (جم)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>العملية</label>
                    <select name="restrictions[${restrictionCount}][operator]">
                        <option value="<">أقل من (<)</option>
                        <option value="<=">أقل أو يساوي (<=)</option>
                        <option value="=">يساوي (=)</option>
                        <option value=">=">أكبر أو يساوي (>=)</option>
                        <option value=">">أكبر من (>)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>القيمة</label>
                    <input type="text" name="restrictions[${restrictionCount}][value]" placeholder="مثال: 2000">
                </div>
                <button type="button" class="remove-restriction-btn" onclick="removeRestriction(this)">حذف</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newRestriction);
        restrictionCount++;
    }

    function removeRestriction(button) {
        const container = document.getElementById('restrictions-container');
        if (container.children.length > 1) {
            button.closest('.restriction-item').remove();
        } else {
            alert('يجب أن يكون لديك حقل قيد واحد على الأقل');
        }
    }
</script>
@endpush
@endsection
