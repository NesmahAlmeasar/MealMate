@extends('layouts.admin_app')

@section('title', 'Edit User: ' . $user->Fname . ' ' . $user->Lname)

@section('content')
    <div class="user-edit-section">
        <h1 class="section-title">✏️ تعديل بيانات المستخدم: {{ $user->Fname }} {{ $user->Lname }}</h1>
        
        {{-- عرض رسائل النجاح أو الأخطاء هنا --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>حدث خطأ!</strong> الرجاء مراجعة الأخطاء في الفورم.
            </div>
        @endif

        {{-- ⭐️ Form التعديل (يستخدم طريقة PUT) ⭐️ --}}
        <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') 
            
            {{-- ⭐️⭐️ استدعاء النموذج الجزئي للتعديل ⭐️⭐️ --}}
            {{-- نمرر $user و $allRoles --}}
            @include('admin.partials._user_form', ['user' => $user, 'allRoles' => $allRoles]) 

            <div class="modal-footer">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">إلغاء والعودة</a>
                <button type="submit" class="btn-primary">حفظ التعديلات</button>
            </div>
        </form>
    </div>
@endsection