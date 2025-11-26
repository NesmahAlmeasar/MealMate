@extends('layouts.admin_app')

@section('title', 'Meals Management')

@push('styles')

<style>
    /* المتغيرات والتصميم الأساسي */
    :root {
        --olive-light: #F8FAF5; /* خلفية رأس الجدول */
        --olive-dark: #6B8E23; /* لون النص الأساسي (الأخضر الزيتوني) */
        --border-color: #E5E7EB;
        --red-accent: #DC2626;
        --blue-primary: #3B82F6;
        --text-dark: #1F2937;
    }

    .meals-container {
        padding: 30px;
    }

    .meals-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    /* تصميم زر "إضافة وجبة جديدة" الجديد (مستوحى من زر المستخدمين) */
    .add-meal-btn {
        background-color: var(--olive-dark); 
        color: white;
        padding: 10px 20px;
        border-radius: 8px; /* زوايا أقل استدارة */
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(107, 142, 35, 0.4); 
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 14px;
    }
    .add-meal-btn:hover {
        background-color: #55751d; /* لون أغمق قليلاً عند التمرير */
        transform: translateY(-1px);
    }
    .add-meal-btn::before {
        content: '+';
        font-size: 1.2em;
        font-weight: bold;
    }

    /* ⭐️ تصميم جدول الوجبات (Meals Table) ⭐️ */
    .meals-table-card {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        background: white;
        border-radius: 10px;
        margin-top: 15px;
    }
    
    .meals-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px; /* مسافة بين الصفوف لجعلها تبدو كبطاقات */
        table-layout: fixed; 
        border-radius: 10px;
        overflow: hidden;
    }

    /* رأس الجدول */
    .meals-table thead th {
        background-color: var(--olive-light);
        color: var(--olive-dark);
        font-weight: 700;
        font-size: 13px;
        padding: 12px 10px;
        text-align: right; 
        border-bottom: 2px solid var(--border-color);
        white-space: nowrap; 
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* جسم الجدول */
    .meals-table tbody td {
        padding: 15px 10px;
        font-size: 14px;
        color: var(--text-dark);
        vertical-align: middle;
        border-bottom: 1px solid #eee;
    }
/* ⭐️ تحديد عرض الأعمدة بشكل موحد للرأس والجسم ⭐️ */
/* نستخدم td:nth-child(n) لتطبيق العرض على كل خلايا العمود */
.meals-table th:nth-child(1),
.meals-table td:nth-child(1) { 
    width: 80px; 
} /* صورة */

.meals-table th:nth-child(2),
.meals-table td:nth-child(2) { 
    width: auto; /* ترك هذا العمود يتمدد قدر الإمكان */
    min-width: 250px; /* ضمان حد أدنى للعرض */
} /* الاسم (الذي يحوي التفاصيل المدمجة) */

.meals-table th:nth-child(3),
.meals-table td:nth-child(3) { 
    width: 120px; 
} /* الحالة */

.meals-table th:nth-child(4),
.meals-table td:nth-child(4) { 
    width: 120px; 
    text-align: left; /* محاذاة لليسار لأزرار الإجراءات */
} /* الإجراءات */

/* تأكد من محاذاة أزرار الإجراءات لليسار في الجسم */
.action-buttons {
    display: flex;
    gap: 10px; 
    align-items: center;
    justify-content: flex-start; /* تم التعديل إلى اليسار (Start) */
}

/* تأكد من محاذاة النص داخل خلايا الجسم لليسار بشكل عام، أو حسب تفضيلك */
.meals-table tbody td {
    padding: 15px 10px;
    font-size: 14px;
    color: var(--text-dark);
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    text-align: right; /* محاذاة النص لليمين (لغة عربية) */
}

/* محاذاة رأس الجدول يجب أن تتطابق مع الجسم */
.meals-table thead th {
    /* ... الأنماط الأخرى ... */
    text-align: right; 
    /* ... */
}
    /* صورة الوجبة */
    .meal-image-thumb {
        width: 50px; 
        height: 50px;
        object-fit: cover;
        border-radius: 8px; /* زوايا مربعة ناعمة (أفضل للصور) */
        border: 1px solid #ddd;
    }
    
    /* تصميم الشارات (Badges) - الحالة */
    .status-badge {
        padding: 6px 14px;
        border-radius: 15px; 
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .status-pending {
        background: #fff3e0; 
        color: #ff9800;
    }
    .status-approved {
        background: #e8f5e9; 
        color: #4caf50;
    }
    .status-rejected {
        background: #ffebee; 
        color: #f44336;
    }

    /* ⭐️ أزرار الإجراءات (Actions) - نفس تصميم المستخدمين ⭐️ */
    .action-buttons {
        display: flex;
        gap: 10px; 
        align-items: center;
        justify-content: flex-start;
    }
    .action-btn {
        border: none;
        background: none;
        cursor: pointer;
        font-size: 16px;
        transition: color 0.2s;
        /* استخدام أيقونات Font Awesome (نفرض أنها متاحة) */
    }
    /* استخدام الألوان المشابهة لتصميم المستخدمين */
    .action-btn.view-btn { color: var(--blue-primary); } /* أزرق للعرض */
    .action-btn.edit-btn { color: #FFA500; } /* برتقالي للتعديل */
    .action-btn.delete-btn { color: var(--red-accent); } /* أحمر للحذف */
    
    /* تنبيه النجاح */
    .alert-success { 
        color: #155724; 
        background-color: #d4edda; 
        border: 1px solid #c3e6cb; 
        padding: 15px; 
        margin-bottom: 20px; 
        border-radius: 8px; 
        font-size: 14px; 
    }
</style>

@endpush
@section('content')
<div class="meals-container">
    <div class="meals-header">
        <h1>Meals Management</h1>
        <a href="{{ route('admin.meals.create') }}" class="add-meal-btn">
             Add New Meal
        </a> 
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card meals-table-card">
        <table class="meals-table">
            <thead>
                <tr>
                    <th >صورة</th>
                    <th>الاسم</th>
                    <th>الحالة</th>
                    <th >الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($meals as $meal)
                    <tr>
                        {{-- 1. الصورة --}}
                        <td>
                            @if($meal->photo_url)
                                <img src="{{ asset('storage/' . $meal->photo_url) }}" alt="{{ $meal->name }}" class="meal-image-thumb">
                            @else
                                <div class="meal-image-thumb" style="background: #ccc; display:flex; align-items:center; justify-content:center; color:white; font-size:10px;">No Image</div>
                            @endif
                        </td>
                        
                        {{-- 2. الاسم (تم دمج تفاصيل الوجبة هنا إذا لزم الأمر) --}}
                        <td>
                            <strong style="font-size: 15px;">{{ $meal->name }}</strong><br>
                            <span style="font-size: 12px; color: #777;">
                                {{ $meal->category->category_name ?? 'N/A' }} | 
                                ${{ number_format($meal->price, 2) }} | 
                                {{ $meal->calories ?? 0 }} kcal
                            </span>
                        </td>
                        
                        {{-- 3. الحالة --}}
                        <td>
                            <span class="status-badge status-{{ strtolower($meal->state) }}">
                                {{ ucfirst($meal->state) }}
                            </span>
                        </td>
                        
                        {{-- 4. الإجراءات --}}
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