@extends('layouts.admin_app')

@section('title', 'Diets Management')

@push('styles')
<style>
    .diets-container {
        padding: 20px;
    }
    .diets-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .add-diet-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        transition: transform 0.2s;
    }
    .add-diet-btn:hover {
        transform: translateY(-2px);
    }
    .diets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }
    .diet-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }
    .diet-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .diet-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .diet-content {
        padding: 20px;
    }
    .diet-title {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }
    .diet-description {
        color: #666;
        font-size: 14px;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .diet-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    .diet-meals-count {
        color: #667eea;
        font-weight: bold;
    }
    .diet-actions {
        display: flex;
        gap: 8px;
    }
    .btn-sm {
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 13px;
        border: none;
        cursor: pointer;
    }
    .btn-view {
        background: #17a2b8;
        color: white;
    }
    .btn-edit {
        background: #ffc107;
        color: #000;
    }
    .btn-delete {
        background: #dc3545;
        color: white;
    }
    .public-badge {
        background: #28a745;
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
    }
    .private-badge {
        background: #6c757d;
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
    }
</style>
@endpush

@section('content')
<div class="diets-container">
    <div class="diets-header">
        <h1>Diets Management</h1>
        <a href="{{ route('specialist.diets.create') }}" class="add-diet-btn">+ Add New Diet</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="diets-grid">
        @forelse($diets as $diet)
            <div class="diet-card" onclick="window.location='{{ route('specialist.diets.show', $diet->diets_id) }}'">
                @if($diet->photo_url)
                    <img src="{{ asset('storage/' . $diet->photo_url) }}" alt="{{ $diet->name }}" class="diet-image">
                @else
                    <img src="{{ asset('images/diet_placeholder.jpg') }}" alt="{{ $diet->name }}" class="diet-image">
                @endif
                
                <div class="diet-content">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <h3 class="diet-title">{{ $diet->name }}</h3>
                        <span class="{{ $diet->is_public ? 'public-badge' : 'private-badge' }}">
                            {{ $diet->is_public ? 'Public' : 'Private' }}
                        </span>
                    </div>
                    
                    <p class="diet-description">{{ $diet->description ?? 'No description available' }}</p>
                    
                    <div class="diet-meta">
                        <span class="diet-meals-count">
                            🍽️ {{ $diet->meals->count() }} Meals
                        </span>
                        
                        <div class="diet-actions" onclick="event.stopPropagation();">
                            <a href="{{ route('specialist.diets.show', $diet->diets_id) }}" class="btn-sm btn-view">View</a>
                            <a href="{{ route('specialist.diets.edit', $diet->diets_id) }}" class="btn-sm btn-edit">Edit</a>
                            <form action="{{ route('specialist.diets.destroy', $diet->diets_id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-sm btn-delete" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
                <p style="font-size: 18px; color: #666; margin-bottom: 20px;">No diets found</p>
                <a href="{{ route('specialist.diets.create') }}" class="add-diet-btn">Create Your First Diet</a>
            </div>
        @endforelse
    </div>
</div>
@endsection