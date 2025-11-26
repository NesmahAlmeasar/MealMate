@extends('layouts.admin_app')

@section('title', 'Add New Meal')

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
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <h1>Add New Meal</h1>
        <p>Fill in the details below to add a new meal to the system</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.meals.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Meal Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price ($) *</label>
                <input type="number" id="price" name="price" step="0.01" value="{{ old('price') }}" required>
                @error('price')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="proper_time">Proper Time</label>
                <select id="proper_time" name="proper_time">
                    <option value="">Select Time</option>
                    <option value="breakfast" {{ old('proper_time') == 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                    <option value="lunch" {{ old('proper_time') == 'lunch' ? 'selected' : '' }}>Lunch</option>
                    <option value="dinner" {{ old('proper_time') == 'dinner' ? 'selected' : '' }}>Dinner</option>
                    <option value="snack" {{ old('proper_time') == 'snack' ? 'selected' : '' }}>Snack</option>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity_g">Quantity (g)</label>
                <input type="number" id="quantity_g" name="quantity_g" step="0.01" value="{{ old('quantity_g') }}">
            </div>

            <div class="form-group">
                <label for="calories">Calories</label>
                <input type="number" id="calories" name="calories" step="0.01" value="{{ old('calories') }}">
            </div>

            <div class="form-group">
                <label for="protein_g">Protein (g)</label>
                <input type="number" id="protein_g" name="protein_g" step="0.01" value="{{ old('protein_g') }}">
            </div>

            <div class="form-group">
                <label for="fat_g">Fat (g)</label>
                <input type="number" id="fat_g" name="fat_g" step="0.01" value="{{ old('fat_g') }}">
            </div>

            <div class="form-group">
                <label for="carbs_g">Carbs (g)</label>
                <input type="number" id="carbs_g" name="carbs_g" step="0.01" value="{{ old('carbs_g') }}">
            </div>

            <div class="form-group">
                <label for="photo">Photo</label>
                <input type="file" id="photo" name="photo" accept="image/*">
            </div>

            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description') }}</textarea>
            </div>

            <div class="ingredients-section">
                <h3>Ingredients</h3>
                <div id="ingredients-container">
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
                </div>
                <button type="button" class="add-ingredient-btn" onclick="addIngredient()">+ Add Ingredient</button>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Create Meal</button>
            <a href="{{ route('admin.meals.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function addIngredient() {
        const container = document.getElementById('ingredients-container');
        const newItem = document.querySelector('.ingredient-item').cloneNode(true);
        
        // Reset values
        newItem.querySelectorAll('select, input').forEach(el => el.value = '');
        
        container.appendChild(newItem);
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
