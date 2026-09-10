@extends('layouts.admin_app')

@section('title', 'لوحة تحكم الأخصائي')

@section('content')
    <div style="padding: var(--spacing-xl);">
        <h1 style="margin-bottom: var(--spacing-xl); font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; color: var(--text-dark);">لوحة تحكم الأخصائي</h1>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--spacing-lg); margin-bottom: var(--spacing-xl);">
            {{-- Pending Meals Card --}}
            <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 700; color: var(--text-dark); margin: 0;">{{ $pendingMeals }}</h3>
                    <p style="color: var(--text-light); margin: var(--spacing-xs) 0 0;">وجبات قيد الانتظار</p>
                </div>
                <div style="width: 50px; height: 50px; border-radius: var(--radius-lg); background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>

            {{-- Approved Meals Card --}}
            <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 700; color: var(--text-dark); margin: 0;">{{ $approvedMeals }}</h3>
                    <p style="color: var(--text-light); margin: var(--spacing-xs) 0 0;">وجبات معتمدة</p>
                </div>
                <div style="width: 50px; height: 50px; border-radius: var(--radius-lg); background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            {{-- My Diets Card --}}
            <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 700; color: var(--text-dark); margin: 0;">{{ $myDietsCount }}</h3>
                    <p style="color: var(--text-light); margin: var(--spacing-xs) 0 0;">حمياتي التي تم إنشاؤها</p>
                </div>
                <div style="width: 50px; height: 50px; border-radius: var(--radius-lg); background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-apple-alt"></i>
                </div>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg); padding-bottom: var(--spacing-md); border-bottom: 1px solid var(--border-color);">
                <h2 style="margin: 0; font-size: clamp(1.1rem, 2vw, 1.3rem); color: var(--text-dark);">أحدث الوجبات المعلقة</h2>
                <a href="{{ route('specialist.meals.pending') }}" class="btn btn-primary" style="font-size: 0.9rem; padding: 0.4rem 1rem;">مراجعة الكل</a>
            </div>
            
            @forelse($recentPendingMeals as $meal)
                <div style="display: flex; align-items: center; padding: var(--spacing-md) 0; border-bottom: 1px solid var(--bg-gray-dark);">
                    <div style="width: 40px; height: 40px; background: #fef3c7; color: #d97706; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin-left: var(--spacing-md); font-size: 1.1rem;">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 2px;">{{ $meal->name }}</div>
                        <div style="font-size: 0.85rem; color: var(--text-light);">
                            {{ $meal->category->category_name ?? 'غير مصنف' }} • ${{ $meal->price }}
                        </div>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-light);">
                        {{ $meal->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: var(--text-light); padding: var(--spacing-lg);">لا توجد وجبات معلقة للمراجعة.</p>
            @endforelse
        </div>
    </div>
@endsection