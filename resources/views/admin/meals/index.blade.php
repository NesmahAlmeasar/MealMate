@extends('layouts.admin_app')

@section('title', 'إدارة الاطباق')

@section('content')
<div>



    <div class="page-header" style="flex-direction: column; align-items: start; gap: 10px;">

    
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">


        
            <h1 class="page-title">إدارة الاطباق</h1>

             <form action="{{ route('admin.meals.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
            <select name="state" id="state" onchange="this.form.submit()" class="form-control" style="width: 200px; padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border-color); font-family: 'Cairo';">
                <option value="">كل الوجبات</option>
                <option value="approved" {{ request('state') == 'approved' ? 'selected' : '' }}>مقبولة (Approved)</option>
                <option value="pending" {{ request('state') == 'pending' ? 'selected' : '' }}>معلقة (Pending)</option>
                <option value="rejected" {{ request('state') == 'rejected' ? 'selected' : '' }}>مرفوضة (Rejected)</option>
            </select>
        </form>

            <a href="{{ route('admin.meals.create') }}" class="btn-primary">
                 <i class="fas fa-plus"></i> إضافة طبق جديدة
            </a> 

            
        </div>

       
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
                                <div style="width: 50px; height: 50px; background: #ccc; border-radius: 8px; display:flex; align-items:center; justify-content:center; color:white; font-size:10px;">لا توجد صورة</div>
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
                            <div class="diet-card-actions" style="justify-content: center;">
                                <a href="{{ route('admin.meals.show', $meal->meals_id) }}" class="diet-action-btn view" title="View Meal">
                                    👁️ </a>
                                <a href="{{ route('admin.meals.edit', $meal->meals_id) }}" class="diet-action-btn edit" title="Edit Meal">
                                    ✏️ </a>
                                <form action="{{ route('admin.meals.destroy', $meal->meals_id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="diet-action-btn delete" title="Delete Meal" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذه الوجبة؟')">
                                        🗑️ </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px;">
                            لم يتم العثور على وجبات. <a href="{{ route('admin.meals.create') }}">أضف وجبتك الأولى</a>
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