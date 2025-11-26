@extends('layouts.admin_app')

@section('title', 'Edit Meal')

@push('styles')
<style>
    .form-container {
        max-width: 1000px;
        margin: 20px auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .form-header {
        margin-bottom: 30px;
    }
    .form-header h1 {
        color: #333;
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
    .ingredients-section {
        grid-column: 1 / -1;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        background: #f8f9fa;
    }
    .ingredient-item {
        display: grid;
        grid-template-columns: 3fr 1fr auto;
        gap: 10px;
        margin-bottom: 10px;
        align-items: end;
    }
    .add-ingredient-btn {
        background: #28a745;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
    }
    .remove-ingredient-btn {
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
    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }
    .current-image {
        max-width: 200px;
        border-radius: 8px;
        margin-top: 10px;
    }
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <h1>Edit Meal: {{ $meal->name }}</h1>
        <p>Update the meal details below</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
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
            <div class="form-group">
                <label for="name">Meal Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $meal->name) }}" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" 
                            {{ old('category_id', $meal->category_id) == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price ($) *</label>
                <input type="number" id="price" name="price" step="0.01" value="{{ old('price', $meal->price) }}" required>
                @error('price')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="proper_time">Proper Time</label>
                <select id="proper_time" name="proper_time">
                    <option value="">Select Time</option>
                    <option value="breakfast" {{ old('proper_time', $meal->proper_time) == 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                    <option value="lunch" {{ old('proper_time', $meal->proper_time) == 'lunch' ? 'selected' : '' }}>Lunch</option>
                    <option value="dinner" {{ old('proper_time', $meal->proper_time) == 'dinner' ? 'selected' : '' }}>Dinner</option>
                    <option value="snack" {{ old('proper_time', $meal->proper_time) == 'snack' ? 'selected' : '' }}>Snack</option>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity_g">Quantity (g)</label>
                <input type="number" id="quantity_g" name="quantity_g" step="0.01" value="{{ old('quantity_g', $meal->quantity_g) }}">
            </div>

            <div class="form-group">
                <label for="calories">Calories</label>
                <input type="number" id="calories" name="calories" step="0.01" value="{{ old('calories', $meal->calories) }}">
            </div>

            <div class="form-group">
                <label for="protein_g">Protein (g)</label>
                <input type="number" id="protein_g" name="protein_g" step="0.01" value="{{ old('protein_g', $meal->protein_g) }}">
            </div>

            <div class="form-group">
                <label for="fat_g">Fat (g)</label>
                <input type="number" id="fat_g" name="fat_g" step="0.01" value="{{ old('fat_g', $meal->fat_g) }}">
            </div>

            <div class="form-group">
                <label for="carbs_g">Carbs (g)</label>
                <input type="number" id="carbs_g" name="carbs_g" step="0.01" value="{{ old('carbs_g', $meal->carbs_g) }}">
            </div>

            <div class="form-group">
                <label for="photo">Photo (leave empty to keep current)</label>
                <input type="file" id="photo" name="photo" accept="image/*">
                @if($meal->photo_url)
                    <img src="{{ asset('storage/' . $meal->photo_url) }}" alt="Current photo" class="current-image">
                @endif
            </div>

            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $meal->description) }}</textarea>
            </div>

            <div class="ingredients-section">
                <h3>Ingredients</h3>
                <div id="ingredients-container">
                    @if($meal->ingredients->count() > 0)
                        @foreach($meal->ingredients as $index => $ingredient)
                            <div class="ingredient-item">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label>Ingredient</label>
                                    <select name="ingredients[]">
                                        <option value="">Select Ingredient</option>
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->ingredients_id }}" 
                                                {{ $ingredient->ingredients_id == $ing->ingredients_id ? 'selected' : '' }}>
                                                {{ $ing->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label>Quantity (g)</label>
                                    <input type="number" name="ingredient_quantities[]" step="0.01" 
                                        value="{{ $ingredient->pivot->quantity_g }}" placeholder="Quantity">
                                </div>
                                <button type="button" class="remove-ingredient-btn" onclick="removeIngredient(this)">Remove</button>
                            </div>
                        @endforeach
                    @else
                        <div class="ingredient-item">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Ingredient</label>
                                <select name="ingredients[]">
                                    <option value="">Select Ingredient</option>
                                    @foreach($ingredients as $ingredient)
                                        <option value="{{ $ingredient->ingredients_id }}">{{ $ingredient->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Quantity (g)</label>
                                <input type="number" name="ingredient_quantities[]" step="0.01" placeholder="Quantity">
                            </div>
                            <button type="button" class="remove-ingredient-btn" onclick="removeIngredient(this)">Remove</button>
                        </div>
                    @endif
                </div>
                <button type="button" class="add-ingredient-btn" onclick="addIngredient()">+ Add Ingredient</button>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Update Meal</button>
            <a href="{{ route('admin.meals.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const ingredientsTemplate = `
        <div class="ingredient-item">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Ingredient</label>
                <select name="ingredients[]">
                    <option value="">Select Ingredient</option>
                    @foreach($ingredients as $ingredient)
                        <option value="{{ $ingredient->ingredients_id }}">{{ $ingredient->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Quantity (g)</label>
                <input type="number" name="ingredient_quantities[]" step="0.01" placeholder="Quantity">
            </div>
            <button type="button" class="remove-ingredient-btn" onclick="removeIngredient(this)">Remove</button>
        </div>
    `;

    function addIngredient() {
        const container = document.getElementById('ingredients-container');
        const temp = document.createElement('div');
        temp.innerHTML = ingredientsTemplate.trim();
        container.appendChild(temp.firstChild);
    }

    function removeIngredient(button) {
        const container = document.getElementById('ingredients-container');
        if (container.children.length > 1) {
            button.closest('.ingredient-item').remove();
        } else {
            alert('You must have at least one ingredient field');
        }
    }
</script>
@endpush
@endsection
