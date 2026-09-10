@extends('layouts.admin_app')

@section('title', 'لوحة تحكم مدير التغذية')

@section('content')
<div class="dashboard-container" style="padding: var(--spacing-xl);">
    <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; color: var(--text-dark); margin-bottom: var(--spacing-sm);">أهلاً بك، {{ Auth::user()->Fname }} (مدير التغذية)</h1>
    <p style="color: var(--text-light); margin-bottom: var(--spacing-xl);">قم بإدارة المكونات الغذائية، الأمراض المزمنة، الحساسية،  من هنا.</p>

    <div class="dashboard-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--spacing-lg);">
        <div class="card" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: var(--spacing-xl);">
            <h3 style="font-size: 1.1rem; color: var(--text-medium); margin-bottom: var(--spacing-md);">المكونات الغذائية</h3>
            <p style="font-size: 2.5rem; font-weight: 800; color: var(--olive-dark); margin-bottom: var(--spacing-lg);">{{ \App\Models\Ingredient::count() }}</p>
            <a href="{{ route('nutrition-manager.ingredients.index') }}" class="btn btn-secondary">إدارة</a>
        </div>
        <div class="card" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: var(--spacing-xl);">
            <h3 style="font-size: 1.1rem; color: var(--text-medium); margin-bottom: var(--spacing-md);">الأمراض المزمنة</h3>
            <p style="font-size: 2.5rem; font-weight: 800; color: var(--red-accent); margin-bottom: var(--spacing-lg);">{{ \App\Models\ChronicDisease::count() }}</p>
            <a href="{{ route('nutrition-manager.chronic-diseases.index') }}" class="btn btn-secondary">إدارة</a>
        </div>
        <div class="card" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: var(--spacing-xl);">
            <h3 style="font-size: 1.1rem; color: var(--text-medium); margin-bottom: var(--spacing-md);">الحساسية</h3>
            <p style="font-size: 2.5rem; font-weight: 800; color: var(--gold-accent); margin-bottom: var(--spacing-lg);">{{ \App\Models\Allergy::count() }}</p>
            <a href="{{ route('nutrition-manager.allergies.index') }}" class="btn btn-secondary">إدارة</a>
        </div>
        <div class="card" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: var(--spacing-xl);">
            <h3 style="font-size: 1.1rem; color: var(--text-medium); margin-bottom: var(--spacing-md);">الادوية الطبية  / الأدوية</h3>
            <p style="font-size: 2.5rem; font-weight: 800; color: var(--blue-accent, #3b82f6); margin-bottom: var(--spacing-lg);">{{ \App\Models\MedicalRecord::count() }}</p>
            <a href="{{ route('nutrition-manager.medications.index') }}" class="btn btn-secondary">إدارة</a>
        </div>
    </div>
</div>
@endsection
