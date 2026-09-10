@extends('layouts.admin_app')

@section('title', 'مكتبة الحميات')

@section('content')
<div style="padding: var(--spacing-xl);">
    <div class="page-header">
        <div>
            <h2>مكتبة الحميات</h2>
        </div>
        
        @if(Auth::check() && (Auth::user()->hasRole('Specialist') || Auth::user()->hasRole('Nutrition Manager')))
        <a href="{{ route('shared.diets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة حمية جديدة
        </a>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--spacing-lg); margin-top: var(--spacing-xl);">
        @forelse($diets as $diet)
            <div class="card" onclick="window.location='{{ route('shared.diets.show', $diet->diets_id) }}'" style="cursor: pointer; padding: 0; overflow: hidden;">
                @if($diet->photo_url)
                    <img src="{{ asset('storage/' . $diet->photo_url) }}" alt="{{ $diet->name }}" style="width: 100%; height: 160px; object-fit: cover;">
                @else
                    <img src="{{ asset('images/diet_placeholder.jpg') }}" alt="{{ $diet->name }}" style="width: 100%; height: 160px; object-fit: cover; background: var(--bg-gray);">
                @endif
                
                <div style="padding: var(--spacing-md);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-sm);">
                        <h3 style="font-size: clamp(0.95rem, 1.75vw, 1.1rem); margin: 0; color: var(--text-dark);">{{ $diet->name }}</h3>
                        <span class="badge badge-{{ $diet->is_public ? 'success' : 'info' }}" style="font-size: 0.7rem;">
                            {{ $diet->is_public ? 'عام' : 'خاص' }}
                        </span>
                    </div>
                    
                    <p style="color: var(--text-light); font-size: clamp(0.7rem, 1.25vw, 0.8rem); margin-bottom: var(--spacing-md); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $diet->description ?? 'لا يوجد وصف متاح' }}</p>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: var(--spacing-sm); border-top: 1px solid var(--border-color);">
                        <span style="color: var(--olive-medium); font-weight: 600; font-size: 0.8rem;">
                            🍽️ {{ $diet->meals->count() }} وجبات
                        </span>
                        
                        <a href="{{ route('shared.diets.show', $diet->diets_id) }}" class="action-btn view" onclick="event.stopPropagation();" title="عرض">
                            👁️
                        </a>
                    </div>
                    
                    @php
                        $canEdit = false;
                        $canDelete = false;
                        
                        if(Auth::user()->hasRole('Nutrition Manager')) {
                            $canEdit = $diet->is_public || $diet->nutritionist_id == Auth::id();
                            $canDelete = $canEdit;
                        } elseif(Auth::user()->hasRole('Specialist')) {
                            $canEdit = !$diet->is_public && $diet->nutritionist_id == Auth::id();
                            $canDelete = $canEdit;
                        }
                    @endphp
                    
                    @if($canEdit || $canDelete)
                    <div class="action-buttons" style="justify-content: center; padding-top: var(--spacing-sm); border-top: 1px solid var(--border-color); margin-top: var(--spacing-sm);">
                        @if($canEdit)
                        <a href="{{ route('shared.diets.edit', $diet->diets_id) }}" class="action-btn edit" title="تعديل" onclick="event.stopPropagation();">
                            ✏️
                        </a>
                        @endif
                        
                        @if($canDelete)
                        <form action="{{ route('shared.diets.destroy', $diet->diets_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الحمية؟');" onclick="event.stopPropagation();">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete" title="حذف">
                                🗑️
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: var(--spacing-2xl); color: var(--text-light);">
                <p>لا توجد حميات متاحة</p>
            </div>
        @endforelse
    </div>
</div>
@endsection