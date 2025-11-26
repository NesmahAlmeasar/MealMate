@extends('layouts.admin_app')

@section('title', 'Meal Details')

@push('styles')
<style>
    .meal-details-container {
        max-width: 1200px;
        margin: 20px auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .meal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #eee;
    }
    .meal-header h1 {
        color: #333;
        margin: 0;
    }
    .action-buttons {
        display: flex;
        gap: 10px;
    }
    .btn {
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        border: none;
        cursor: pointer;
    }
    .btn-edit {
        background: #ffc107;
        color: #000;
    }
    .btn-back {
        background: #6c757d;
        color: white;
    }
    .btn-delete {
        background: #dc3545;
        color: white;
    }
    .meal-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    .meal-image-section {
        text-align: center;
    }
    .meal-image-large {
        width: 100%;
        max-width: 500px;
        height: 400px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .meal-info-section {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .info-group {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }
    .info-group h3 {
        margin: 0 0 10px 0;
        color: #667eea;
        font-size: 16px;
    }
    .info-group p {
        margin: 5px 0;
        color: #555;
    }
    .status-badge {
        padding: 6px 15px;
        border-radius: 15px;
        font-size: 14px;
        font-weight: bold;
        display: inline-block;
    }
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    .status-approved {
        background: #d4edda;
        color: #155724;
    }
    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }
    .nutrition-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .nutrition-item {
        background: white;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
    }
    .nutrition-item strong {
        display: block;
        color: #667eea;
        font-size: 18px;
    }
    .ingredients-list {
        list-style: none;
        padding: 0;
    }
    .ingredients-list li {
        background: white;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 5px;
        border-left: 3px solid #667eea;
    }
</style>
@endpush

@section('content')
<div class="meal-details-container">
    <div class="meal-header">
        <div>
            <h1>{{ $meal->name }}</h1>
            <span class="status-badge status-{{ $meal->state }}">
                {{ ucfirst($meal->state) }}
            </span>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.meals.index') }}" class="btn btn-back">← Back to List</a>
            <a href="{{ route('admin.meals.edit', $meal->meals_id) }}" class="btn btn-edit">Edit</a>
            <form action="{{ route('admin.meals.destroy', $meal->meals_id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this meal?')">Delete</button>
            </form>
        </div>
    </div>

    <div class="meal-content">
        <div class="meal-image-section">
            @if($meal->photo_url)
                <img src="{{ asset('storage/' . $meal->photo_url) }}" alt="{{ $meal->name }}" class="meal-image-large">
            @else
                <img src="{{ asset('images/meal_placeholder.jpg') }}" alt="No image" class="meal-image-large">
            @endif
        </div>

        <div class="meal-info-section">
            <div class="info-group">
                <h3>Basic Information</h3>
                <p><strong>Category:</strong> {{ $meal->category->category_name ?? 'N/A' }}</p>
                <p><strong>Price:</strong> ${{ number_format($meal->price, 2) }}</p>
                <p><strong>Proper Time:</strong> {{ ucfirst($meal->proper_time ?? 'Any time') }}</p>
                <p><strong>Quantity:</strong> {{ $meal->quantity_g ?? 'N/A' }}g</p>
            </div>

            <div class="info-group">
                <h3>Description</h3>
                <p>{{ $meal->description ?? 'No description available' }}</p>
            </div>

            <div class="info-group">
                <h3>Nutritional Information</h3>
                <div class="nutrition-grid">
                    <div class="nutrition-item">
                        <strong>{{ $meal->calories ?? 0 }}</strong>
                        <span>Calories</span>
                    </div>
                    <div class="nutrition-item">
                        <strong>{{ $meal->protein_g ?? 0 }}g</strong>
                        <span>Protein</span>
                    </div>
                    <div class="nutrition-item">
                        <strong>{{ $meal->carbs_g ?? 0 }}g</strong>
                        <span>Carbs</span>
                    </div>
                    <div class="nutrition-item">
                        <strong>{{ $meal->fat_g ?? 0 }}g</strong>
                        <span>Fat</span>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <h3>Ingredients ({{ $meal->ingredients->count() }})</h3>
                @if($meal->ingredients->count() > 0)
                    <ul class="ingredients-list">
                        @foreach($meal->ingredients as $ingredient)
                            <li>
                                <strong>{{ $ingredient->name_ar }}</strong>
                                @if($ingredient->pivot->quantity_g)
                                    <span>({{ $ingredient->pivot->quantity_g }}g)</span>
                                @endif
                                <br>
                                <small style="color: #666;">
                                    Cal: {{ $ingredient->calories ?? 0 }} | 
                                    P: {{ $ingredient->protein_g ?? 0 }}g | 
                                    C: {{ $ingredient->carbs_g ?? 0 }}g | 
                                    F: {{ $ingredient->fat_g ?? 0 }}g
                                    @if($ingredient->is_vegan) | 🌱 Vegan @endif
                                    @if($ingredient->has_gluten) | ⚠️ Gluten @endif
                                    @if($ingredient->has_dairy) | 🥛 Dairy @endif
                                </small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No ingredients listed</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
