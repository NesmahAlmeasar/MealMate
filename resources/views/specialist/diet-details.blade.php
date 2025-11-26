@extends('layouts.admin_app')

@section('title', 'Diet Details')

@push('styles')
<style>
    .diet-details-container {
        max-width: 1200px;
        margin: 20px auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .diet-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-top: 2px solid #eee;
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
    .diet-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    .diet-image-section {
        text-align: center;
    }
    .diet-image-large {
        width: 100%;
        max-width: 500px;
        height: 400px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .info-group {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .info-group h3 {
        margin: 0 0 10px 0;
        color: #667eea;
        font-size: 16px;
    }
    .meals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }
    .meal-card-small {
        background: white;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
    .restriction-item {
        background: white;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 5px;
        border-left: 3px solid #667eea;
    }
</style>
@endpush

@section('content')
<div class="diet-details-container">
    <div class="diet-header">
        <div>
            <h1>{{ $diet->name }}</h1>
            <span class="status-badge {{ $diet->is_public ? 'status-approved' : 'status-pending' }}">
                {{ $diet->is_public ? 'Public' : 'Private' }}
            </span>
        </div>
        <div class="action-buttons">
            <a href="{{ route('specialist.diets.index') }}" class="btn btn-back">← Back to List</a>
            <a href="{{ route('specialist.diets.edit', $diet->diets_id) }}" class="btn btn-edit">Edit</a>
            <form action="{{ route('specialist.diets.destroy', $diet->diets_id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </div>
    </div>

    <div class="diet-content">
        <div class="diet-image-section">
            @if($diet->photo_url)
                <img src="{{ asset('storage/' . $diet->photo_url) }}" alt="{{ $diet->name }}" class="diet-image-large">
            @else
                <img src="{{ asset('images/diet_placeholder.jpg') }}" alt="No image" class="diet-image-large">
            @endif
        </div>

        <div>
            <div class="info-group">
                <h3>Description</h3>
                <p>{{ $diet->description ?? 'No description available' }}</p>
            </div>

            @if($diet->warning)
                <div class="info-group">
                    <h3>⚠️ Warnings</h3>
                    <p>{{ $diet->warning }}</p>
                </div>
            @endif

            @if($diet->advice)
                <div class="info-group">
                    <h3>💡 Advice</h3>
                    <p>{{ $diet->advice }}</p>
                </div>
            @endif

            <div class="info-group">
                <h3>Created By</h3>
                <p>{{ $diet->nutritionist->user->Fname ?? 'Unknown' }} {{ $diet->nutritionist->user->Lname ?? '' }}</p>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px;">
        <div class="info-group">
            <h3>Meals in This Diet ({{ $diet->meals->count() }})</h3>
            @if($diet->meals->count() > 0)
                <div class="meals-grid">
                    @foreach($diet->meals as $meal)
                        <div class="meal-card-small">
                            <strong>{{ $meal->name }}</strong><br>
                            <small style="color: #666;">
                                {{ $meal->category->category_name ?? 'N/A' }}<br>
                                {{ $meal->calories ?? 0 }} kcal
                            </small>
                        </div>
                    @endforeach
                </div>
            @else
                <p>No meals added to this diet yet</p>
            @endif
        </div>

        <div class="info-group">
            <h3>Nutritional Restrictions ({{ $diet->restrictions->count() }})</h3>
            @if($diet->restrictions->count() > 0)
                @foreach($diet->restrictions as $restriction)
                    <div class="restriction-item">
                        <strong>{{ $restriction->restriction ?? 'Restriction' }}</strong><br>
                        <small>
                            {{ ucfirst(str_replace('_', ' ', $restriction->field_name)) }} 
                            {{ $restriction->operator }} 
                            {{ $restriction->value }}
                        </small>
                    </div>
                @endforeach
            @else
                <p>No restrictions defined</p>
            @endif
        </div>
    </div>
</div>
@endsection
