@extends('layouts.admin_app')

@section('title', 'إضافة دواء / دواء شائع الاستخدام')

@section('content')
<div class="container" style="padding: 20px; max-width: 600px; margin: 0 auto;">
    <h2>إضافة دواء / دواء شائع الاستخدام جديد</h2>
    
    <form action="{{ route('nutrition-manager.medications.store') }}" method="POST" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @csrf
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>اسم الدواء</label>
            <input type="text" name="medical_record" value="{{ old('medical_record') }}" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;" required>
            @error('medical_record')
                <div style="color: red; font-size: 0.9em; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">حفظ</button>
        <a href="{{ route('nutrition-manager.medications.index') }}" style="margin-left: 10px; color: #666; text-decoration: none;">إلغاء</a>
    </form>
</div>
@endsection
