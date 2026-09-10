@extends('layouts.admin_app')

@section('title', 'إضافة مكون غذائي')

@section('content')
<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>إضافة مكون جديد</h2>
    
    <form action="{{ route('nutrition-manager.ingredients.store') }}" method="POST" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @csrf
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>الاسم الشائع </label>
            <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;" required>
            @error('name_ar')
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>المصطلح (USDA)</label>
            <input type="text" name="usda_term" value="{{ old('usda_term') }}" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;">
            @error('usda_term')
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 15px;">
            <div class="form-group">
                <label> السعرات </label>
                <input type="number" step="0.01" name="calories" value="{{ old('calories') }}" class="form-control" style="width: 100px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                @error('calories')
                    <div style="color: red; font-size: 0.9em; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label> بروتين (جم) </label>
                <input type="number" step="0.01" name="protein_g" value="{{ old('protein_g') }}" class="form-control" style="width: 100px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                @error('protein_g')
                    <div style="color: red; font-size: 0.9em; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label> دهون (جم) </label>
                <input type="number" step="0.01" name="fat_g" value="{{ old('fat_g') }}" class="form-control" style="width: 100px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                @error('fat_g')
                    <div style="color: red; font-size: 0.9em; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label> كربوهيدرات (جم) </label>
                <input type="number" step="0.01" name="carbs_g" value="{{ old('carbs_g') }}" class="form-control" style="width: 100px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                @error('carbs_g')
                    <div style="color: red; font-size: 0.9em; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="margin-right: 15px;"><input type="checkbox" name="is_vegan" value="1" {{ old('is_vegan') ? 'checked' : '' }}> نباتي؟</label>
            <label style="margin-right: 15px;"><input type="checkbox" name="has_gluten" value="1" {{ old('has_gluten') ? 'checked' : '' }}> يحتوي على جلوتين؟</label>
            <label><input type="checkbox" name="has_dairy" value="1" {{ old('has_dairy') ? 'checked' : '' }}> يحتوي على ألبان؟</label>
        </div>

        <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">حفظ المكون</button>
        <a href="{{ route('nutrition-manager.ingredients.index') }}" style="margin-left: 10px; color: #666; text-decoration: none;">إلغاء</a>
    </form>
</div>
@endsection
