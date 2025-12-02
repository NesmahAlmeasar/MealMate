@extends('layouts.admin_app')

@section('title', 'Meals Management')

@section('content')
<div>
    <div class="page-header">
        <h1 class="page-title">Meals Management</h1>
        <a href="{{ route('admin.meals.create') }}" class="btn-primary">
             <i class="fas fa-plus"></i> Add New Meal
        </a> 
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="col-img">صورة</th>
                    <th>الاسم</th>
                    <th class="col-status">الحالة</th>
                    <th class="col-actions">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($meals as $meal)
                    <tr>
                        {{-- 1. Image --}}
                        <td>
                            @if($meal->photo_url)
                                <img src="{{ asset('storage/' . $meal->photo_url) }}" alt="{{ $meal->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            @else
                                <div style="width: 50px; height: 50px; background: #ccc; border-radius: 8px; display:flex; align-items:center; justify-content:center; color:white; font-size:10px;">No Image</div>
                            @endif
                        </td>
                        
                        {{-- 2. Name --}}
                        <td>
                            <strong style="font-size: 15px;">{{ $meal->name }}</strong><br>
                            <span style="font-size: 12px; color: #777;">
                                {{ $meal->category->category_name ?? 'N/A' }} | 
                                ${{ number_format($meal->price, 2) }} | 
                                {{ $meal->calories ?? 0 }} kcal
                            </span>
                        </td>
                        
                        {{-- 3. Status --}}
                        <td>
                            <span class="status-badge status-{{ strtolower($meal->state) }}">
                                {{ ucfirst($meal->state) }}
                            </span>
                        </td>
                        
                        {{-- 4. Actions --}}
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.meals.show', $meal->meals_id) }}" class="action-btn view-btn" title="View Meal">
                                    <i class="fas fa-eye"></i> </a>
                                <a href="{{ route('admin.meals.edit', $meal->meals_id) }}" class="action-btn edit-btn" title="Edit Meal">
                                    <i class="fas fa-edit"></i> </a>
                                <form action="{{ route('admin.meals.destroy', $meal->meals_id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete Meal" onclick="return confirm('Are you sure you want to delete this meal?')">
                                        <i class="fas fa-trash"></i> </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px;">
                            No meals found. <a href="{{ route('admin.meals.create') }}">Add your first meal</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $meals->links() }}
    </div>
</div>
@endsection