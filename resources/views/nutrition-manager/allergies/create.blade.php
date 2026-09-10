@extends('layouts.admin_app')

@section('title', 'إضافة حساسية')

@section('content')
<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>إضافة حساسية جديدة</h2>
    
    <form action="{{ route('nutrition-manager.allergies.store') }}" method="POST" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @csrf
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label>اسم الحساسية</label>
            <input type="text" name="allergies" value="{{ old('allergies') }}" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;" required>
            @error('allergies')
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 10px;">المكونات المرتبطة (اختر كل ما ينطبق)</label>
            <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                @foreach($ingredients as $ingredient)
                    <div style="margin-bottom: 5px;">
                        <label>
                            <input type="checkbox" name="ingredients[]" value="{{ $ingredient->ingredients_id }}"> 
                            {{ $ingredient->name_ar }} 
                            @if($ingredient->usda_term) <small style="color: #888;">({{ $ingredient->usda_term }})</small> @endif
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">حفظ</button>
        <a href="{{ route('nutrition-manager.allergies.index') }}" style="margin-left: 10px; color: #666; text-decoration: none;">إلغاء</a>
    </form>
</div>
@endsection
