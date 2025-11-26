@extends('layouts.admin_app')

@section('title', 'Edit Diet')

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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
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
        <h1>Edit Diet: {{ $diet->name }}</h1>
        <p>Update the diet details below</p>
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
                <label for="name">Diet Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $diet->name) }}" required>
            </div>

            <div class="form-group">
                <label for="photo">Photo (leave empty to keep current)</label>
                <input type="file" id="photo" name="photo" accept="image/*">
                @if($diet->photo_url)
                    <img src="{{ asset('storage/' . $diet->photo_url) }}" alt="Current photo" class="current-image">
                @endif
            </div>

            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $diet->description) }}</textarea>
            </div>

            <div class="form-group full-width">
                <label for="warning">Warnings</label>
                <textarea id="warning" name="warning" placeholder="Any warnings or precautions for this diet">{{ old('warning', $diet->warning) }}</textarea>
            </div>

            <div class="form-group full-width">
                <label for="advice">Advice</label>
                <textarea id="advice" name="advice" placeholder="Tips and advice for following this diet">{{ old('advice', $diet->advice) }}</textarea>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public', $diet->is_public) ? 'checked' : '' }}>
                    <label for="is_public" style="margin-bottom: 0;">Make this diet public</label>
                </div>
            </div>

            <div class="meals-selection">
                <h3>Select Meals for This Diet</h3>
                <p style="color: #666; margin-bottom: 15px;">Choose the meals that are part of this diet plan</p>
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
                <h3>Nutritional Restrictions</h3>
                <p style="color: #666; margin-bottom: 15px;">Update restrictions for this diet</p>
                <div id="restrictions-container">
                    @forelse($diet->restrictions as $index => $restriction)
                        <div class="restriction-item">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Description</label>
                                <input type="text" name="restrictions[{{ $index }}][restriction]" value="{{ $restriction->restriction }}" placeholder="e.g., Maximum daily calories">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Field</label>
                                <select name="restrictions[{{ $index }}][field_name]">
                                    <option value="">Select Field</option>
                                    <option value="calories" {{ $restriction->field_name == 'calories' ? 'selected' : '' }}>Calories</option>
                                    <option value="protein_g" {{ $restriction->field_name == 'protein_g' ? 'selected' : '' }}>Protein (g)</option>
                                    <option value="carbs_g" {{ $restriction->field_name == 'carbs_g' ? 'selected' : '' }}>Carbs (g)</option>
                                    <option value="fat_g" {{ $restriction->field_name == 'fat_g' ? 'selected' : '' }}>Fat (g)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Operator</label>
                                <select name="restrictions[{{ $index }}][operator]">
                                    <option value="<" {{ $restriction->operator == '<' ? 'selected' : '' }}>Less than (<)</option>
                                    <option value="<=" {{ $restriction->operator == '<=' ? 'selected' : '' }}>Less or equal (<=)</option>
                                    <option value="=" {{ $restriction->operator == '=' ? 'selected' : '' }}>Equal (=)</option>
                                    <option value=">=" {{ $restriction->operator == '>=' ? 'selected' : '' }}>Greater or equal (>=)</option>
                                    <option value=">" {{ $restriction->operator == '>' ? 'selected' : '' }}>Greater than (>)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Value</label>
                                <input type="text" name="restrictions[{{ $index }}][value]" value="{{ $restriction->value }}" placeholder="e.g., 2000">
                            </div>
                            <button type="button" class="remove-restriction-btn" onclick="removeRestriction(this)">Remove</button>
                        </div>
                    @empty
                        <div class="restriction-item">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Description</label>
                                <input type="text" name="restrictions[0][restriction]" placeholder="e.g., Maximum daily calories">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Field</label>
                                <select name="restrictions[0][field_name]">
                                    <option value="">Select Field</option>
                                    <option value="calories">Calories</option>
                                    <option value="protein_g">Protein (g)</option>
                                    <option value="carbs_g">Carbs (g)</option>
                                    <option value="fat_g">Fat (g)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Operator</label>
                                <select name="restrictions[0][operator]">
                                    <option value="<">Less than (<)</option>
                                    <option value="<=">Less or equal (<=)</option>
                                    <option value="=">Equal (=)</option>
                                    <option value=">=">Greater or equal (>=)</option>
                                    <option value=">">Greater than (>)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Value</label>
                                <input type="text" name="restrictions[0][value]" placeholder="e.g., 2000">
                            </div>
                            <button type="button" class="remove-restriction-btn" onclick="removeRestriction(this)">Remove</button>
                        </div>
                    @endforelse
                </div>
                <button type="button" class="add-restriction-btn" onclick="addRestriction()">+ Add Restriction</button>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Update Diet</button>
            <a href="{{ route('specialist.diets.index') }}" class="btn-cancel">Cancel</a>
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
                    <label>Description</label>
                    <input type="text" name="restrictions[${restrictionCount}][restriction]" placeholder="e.g., Maximum daily calories">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Field</label>
                    <select name="restrictions[${restrictionCount}][field_name]">
                        <option value="">Select Field</option>
                        <option value="calories">Calories</option>
                        <option value="protein_g">Protein (g)</option>
                        <option value="carbs_g">Carbs (g)</option>
                        <option value="fat_g">Fat (g)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Operator</label>
                    <select name="restrictions[${restrictionCount}][operator]">
                        <option value="<">Less than (<)</option>
                        <option value="<=">Less or equal (<=)</option>
                        <option value="=">Equal (=)</option>
                        <option value=">=">Greater or equal (>=)</option>
                        <option value=">">Greater than (>)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Value</label>
                    <input type="text" name="restrictions[${restrictionCount}][value]" placeholder="e.g., 2000">
                </div>
                <button type="button" class="remove-restriction-btn" onclick="removeRestriction(this)">Remove</button>
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
            alert('You must have at least one restriction field');
        }
    }
</script>
@endpush
@endsection
