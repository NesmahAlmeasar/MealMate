@extends('layouts.admin_app')

@section('title', auth()->user()->hasRole('Specialist') ? 'Diet Plans' : 'Diet Library')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Olive Theme Variables (approximated from user snippet) */
    :root {
        --text-dark: #374151;
        --text-light: #6B7280;
        --olive-light: #E0E7D1; /* Light olive/sage */
        --olive-medium: #A3B18A;
        --olive-dark: #588157;
        --olive-very-light: #F3F6EE;
        --border-color: #E5E7EB;
        --blue-primary: #3B82F6;
        --red-accent: #EF4444;
    }

    /* Header Styles */
    .diets-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .diets-title {
        font-size: 24px;
        font-weight: bold;
        color: var(--text-dark);
    }

    /* Buttons */
    .btn-primary {
        background-color: var(--olive-dark);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #3A5A40;
    }

    .btn-secondary {
        background-color: white;
        border: 1px solid var(--olive-dark);
        color: var(--olive-dark);
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .btn-secondary:hover {
        background-color: var(--olive-very-light);
    }

    /* Add Diet Section (Empty State / CTA) */
    .add-diet-section {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        border: 1px solid var(--border-color);
        margin-bottom: 25px;
        text-align: center;
    }

    .add-diet-icon {
        font-size: 40px;
        margin-bottom: 10px;
    }

    .add-diet-text {
        font-size: 14px;
        color: var(--text-light);
        margin-bottom: 12px;
    }

    /* Grid Layout */
    .diets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    /* Card Styles */
    .diet-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
    }

    .diet-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .diet-card-image {
        width: 100%;
        height: 180px;
        background: linear-gradient(135deg, var(--olive-light), var(--olive-medium));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        overflow: hidden;
    }

    .diet-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .diet-card-content {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .diet-card-title {
        font-size: 16px;
        font-weight: bold;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .diet-card-description {
        font-size: 13px;
        color: var(--text-light);
        line-height: 1.5;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .diet-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid var(--border-color);
        margin-top: auto;
    }

    .diet-card-tag {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .diet-tag-public {
        background-color: #DBEAFE;
        color: var(--blue-primary);
    }

    .diet-tag-private {
        background-color: #FEE2E2;
        color: var(--red-accent);
    }

    .diet-card-actions {
        display: flex;
        gap: 8px;
    }

    .diet-action-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: none;
        background-color: var(--olive-very-light);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
        color: inherit;
    }

    .diet-action-btn:hover {
        background-color: var(--olive-light);
    }

    .diet-action-btn.edit:hover {
        background-color: #DBEAFE;
        color: var(--blue-primary);
    }

    .diet-action-btn.delete {
        /* Styling for delete specifically if needed */
    }

    .diet-action-btn.delete:hover {
        background-color: #FEE2E2;
        color: var(--red-accent);
    }

    /* Responsive Queries */
    @media (max-width: 768px) {
        .diets-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .diets-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
    }

    @media (max-width: 480px) {
        .diets-grid {
            grid-template-columns: 1fr;
        }

        .diet-card-image {
            height: 150px;
            font-size: 40px;
        }
    }
</style>
@endpush

@section('content')
<div class="diets-container">
    
    <!-- Header Section -->
    <div class="diets-header">
        <h1 class="diets-title">{{ auth()->user()->hasRole('Specialist') ? 'Diet Plans' : 'Diet Library' }}</h1>
        @if(auth()->user()->hasRole('Specialist'))
            <a href="{{ route('specialist.diets.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i> Add New Diet
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- CTA Section for Specialists (Optional - visible if no diets or just as a quick action) -->
    @if(auth()->user()->hasRole('Specialist') && $diets->isEmpty())
    <div class="add-diet-section">
        <div class="add-diet-icon">➕</div>
        <div class="add-diet-text">Create a new diet plan for your clients</div>
        <a href="{{ route('specialist.diets.create') }}" class="btn-secondary">
            <i class="fas fa-plus"></i> Create Diet Plan
        </a>
    </div>
    @endif

    <!-- Diets Grid -->
    <div class="diets-grid">
        @forelse($diets as $diet)
            @php
                $showRoute = auth()->user()->hasRole('Specialist') 
                    ? route('specialist.diets.show', $diet->diets_id) 
                    : route('admin.diets.show', $diet->diets_id);
            @endphp
            <div class="diet-card" onclick="window.location='{{ $showRoute }}'">
                <div class="diet-card-image">
                    @if($diet->photo_url)
                        <img src="{{ asset('storage/' . $diet->photo_url) }}" alt="{{ $diet->name }}">
                    @else
                        <!-- Random fruit/veg emoji based on ID for variety -->
                        {{ ['🍎', '🥦', '🥑', '🥩', '🥗', '🥕'][$diet->diets_id % 6] }}
                    @endif
                </div>
                
                <div class="diet-card-content">
                    <div class="diet-card-title">{{ $diet->name }}</div>
                    <div class="diet-card-description">
                        {{ $diet->description ?? 'No description available for this diet plan.' }}
                    </div>
                    
                    <div class="diet-card-footer">
                        <span class="diet-card-tag {{ $diet->is_public ? 'diet-tag-public' : 'diet-tag-private' }}">
                            {{ $diet->is_public ? 'Public' : 'Private' }}
                        </span>

                        <div class="diet-card-actions" onclick="event.stopPropagation();">
                             @if(auth()->user()->hasRole('Specialist'))
                                <a href="{{ route('specialist.diets.edit', $diet->diets_id) }}" class="diet-action-btn edit" title="Edit">
                                    ✏️
                                </a>
                                <form action="{{ route('specialist.diets.destroy', $diet->diets_id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="diet-action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this diet?')">
                                        🗑️
                                    </button>
                                </form>
                            @else
                                <a href="{{ $showRoute }}" class="diet-action-btn edit" title="View Details">
                                    👁️
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            @if(!auth()->user()->hasRole('Specialist'))
                <div class="empty-state">
                    <div class="empty-state-icon">📂</div>
                    <div class="empty-state-title">No Diets Available</div>
                    <div class="empty-state-text">There are currently no diet plans in the library.</div>
                </div>
            @endif
        @endforelse
    </div>
</div>
@endsection
