@extends('layouts.admin_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'User Management')

{{-- 2. إضافة ملف الـ CSS الخاص بالجدول --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
    
    <style>


        
        /* =============================================== */
        /* ⭐️ 2. CSS الخاص بالجدول والمودال (Modal) ⭐️ */
        /* =============================================== */

        .users-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px; /* مسافة بين الصفوف */
            table-layout: fixed; /* لتوزيع الأعمدة بالتساوي */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background: white;
            border-radius: 10px;
            margin-top: 15px;
        }

        .users-table thead th {
            background-color: #F8FAF5;
            color: var(--olive-dark, #6B8E23);
            font-weight: 700;
            font-size: 13px;
            padding: 12px 10px;
            text-align: right; 
            border-bottom: 2px solid var(--border-color, #E5E7EB);
            /* ⭐️⭐️ الإصلاح 1: منع تداخل العناوين ⭐️⭐️ */
            white-space: nowrap; 
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .users-table tbody td {
            padding: 15px 10px;
            font-size: 14px;
            color: var(--text-dark, #1F2937);
            vertical-align: middle;
            border-bottom: 1px solid #eee;
        }

        /* تنسيق الأعمدة */
        .users-table th:nth-child(1) { width: 50px; } 
        .users-table th:nth-child(2) { width: 25%; } 
        .users-table th:nth-child(3) { width: 25%; } 
        .users-table th:nth-child(4) { width: 10%; } 
        .users-table th:nth-child(5) { width: 15%; } 
        .users-table th:nth-child(6) { width: 120px; } /* عرض ثابت للإجراءات */

        /* تنسيق الأزرار داخل الجدول */
        .users-table td:last-child {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-start;
        }

        /* تنسيق الأدوار (Roles) */
        .diet-tag {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }
        .diet-tag.diet-admin { background-color: #FEE2E2; color: #DC2626; }
        .diet-tag.diet-specialist { background-color: #DBEAFE; color: #2563EB; }
        .diet-tag.diet-client { background-color: #D1FAE5; color: #059669; }

        /* تنسيق أزرار الإجراء (Actions) */
        .action-btn {
            border: none;
            background: none;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.2s;
        }
        .action-btn i.fa-trash { color: var(--red-accent, #DC2626); }
        .action-btn i.fa-edit { color: var(--blue-primary, #3B82F6); }
        .action-btn i.fa-envelope { color: var(--olive-dark, #6B8E23); }

        /* ستايل المودال */
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        /* لتفعيل العرض عند وجود أخطاء */
        .modal-overlay[style*="display: flex"] {
            display: flex !important;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 600px;
            animation: slideIn 0.3s ease-out;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color, #E5E7EB);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-dark, #1F2937);
        }
        .modal-close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s;
        }
        .modal-close:hover { color: #333; }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color, #E5E7EB);
        }
        .btn-secondary {
            background-color: #f3f4f6;
            color: #333;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-primary {
            background-color: var(--olive-dark, #6B8E23);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        
        /* تنبيهات النجاح والفشل */
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; }
        .alert-success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .alert-danger ul { margin: 0; padding-left: 20px; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideIn { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .users-table { border-spacing: 0; }
            .users-table thead { display: none; }
            .users-table tr.user-row {
                display: block; width: 100%; margin-bottom: 15px;
                border: 1px solid #ddd; border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }
            .users-table td {
                display: flex; justify-content: space-between; align-items: center;
                padding: 10px 15px; border-bottom: 1px solid #eee; text-align: right;
            }
            .users-table td::before {
                content: attr(data-label); font-weight: 600;
                color: var(--olive-dark, #6B8E23); padding-left: 10px; text-align: left;
            }
            .users-table td[data-label="الإجراءات"] { justify-content: flex-end; gap: 15px; }
        }
    </style>
@endpush

{{-- 3. المحتوى (الجدول) --}}
@section('content')
    <div class="users-section">
        <div class="section-header">
            <h1 class="section-title" data-i18n="userManagement">Diet Client Management</h1>
            <button class="btn-primary add-user-btn" id="openAddUserModal">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="addNewClient">Add New Client</span>
            </button>
        </div>

        {{-- رسائل التنبيه --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>حدث خطأ!</strong> الرجاء مراجعة البيانات:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card users-table-card">
            <table class="users-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">صورة</th>
                        <th style="width: 20%;">اسم العميل</th>
                        <th style="width: 25%;">البريد الإلكتروني</th>
                        <th style="width: 15%;">الدور / الحالة</th> 
                        <th style="width: 120px;">الإجراءات</th> 
                    </tr>
                </thead>
                <tbody>
                    @if ($users->isNotEmpty())
                        @foreach ($users as $user)
                        <tr class="user-row">
                            {{-- 1. الصورة --}}
                            <td data-label="صورة">
                                @if ($user->photo_url)
                                    <img src="{{ asset('storage/' . $user->photo_url) }}" alt="{{ $user->Fname }}" class="client-avatar" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                                @else
                                    <div class="client-avatar" style="width:40px; height:40px; background: #ccc; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white;">{{ substr($user->Fname, 0, 1) }}</div>
                                @endif
                            </td>
                            
                            {{-- 2. الاسم الكامل --}}
                            <td data-label="اسم العميل">
                                <a href="{{ url('admin/users/profile/' . $user->user_id) }}" class="client-name-link" style="font-weight:bold; color:#333; text-decoration:none;">{{ $user->Fname }} {{ $user->Lname }}</a>
                            </td>

                            {{-- 3. البريد (مهم للعرض) --}}
                            <td data-label="البريد الإلكتروني">
                                {{ $user->email }}
                            </td>
                            
                            {{-- 4. الدور --}}
                            <td data-label="الدور / الحالة">
                                @if ($user->roles->isNotEmpty())
                                    @foreach($user->roles as $role)
                                        <span class="diet-tag diet-{{ strtolower($role->name) }}">{{ $role->name }}</span>
                                    @endforeach
                                @else
                                    <span class="diet-tag diet-client">عميل</span>
                                @endif
                            </td>
                            
                            {{-- 5. الإجراءات --}}
                            <td data-label="الإجراءات">
                                <a href="{{ url('admin/messages?user=' . $user->user_id) }}" class="action-btn message-btn" title="Message Client"><i class="fas fa-envelope"></i></a>
                               
                                {{-- تجهيز البيانات كمتغير PHP لجعل الكود أنظف وأكثر استقراراً --}}
@php
    $userData = [
        "id" => $user->user_id,
        "Fname" => $user->Fname,
        "Lname" => $user->Lname,
        "email" => $user->email,
        "phone" => $user->phone,
        "role_names" => $user->roles->pluck('name')->toArray(),
        "account_state" => $user->account_state,
    ];
@endphp

<a href="#" 
   class="action-btn edit-btn" 
   title="Edit Client Data" 
   data-user-id="{{ $user->user_id }}"
   {{-- استخدام json_encode مباشرة داخل الأقواس المزدوجة --}}
   data-user-data="{{ json_encode($userData) }}">
    <i class="fas fa-edit"></i>
</a>
                                <button class="action-btn delete-btn" title="Delete Client" data-user-id="{{ $user->user_id }}"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="user-row"><td colspan="5" style="text-align: center; padding: 20px;">لم يتم إضافة أي مستخدمين بعد.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. المودال (الإضافة/التعديل) --}}
    <div id="addUserModal" class="modal-overlay" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="modal-content">
            
            <div class="modal-header">
                <h2 class="modal-title">إضافة مستخدم جديد</h2>
                <span class="modal-close" id="closeAddUserModal">&times;</span>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- استدعاء الفورم الجزئي --}}
                @include('admin.partials._user_form', ['allRoles' => $allRoles ?? []]) 

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="cancelAddUserModal">إلغاء</button>
                    <button type="submit" class="btn-primary">حفظ المستخدم</button>
                </div>
            </form>

        </div>
    </div>

    {{-- فورم الحذف المخفي --}}
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE') 
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            const modal = document.getElementById('addUserModal');
            const openBtn = document.getElementById('openAddUserModal');
            const closeBtn = document.getElementById('closeAddUserModal');
            const cancelBtn = document.getElementById('cancelAddUserModal');
            const form = modal ? modal.querySelector('form') : null;
            const modalTitle = modal ? modal.querySelector('.modal-title') : null;

            function openModal() { if (modal) { modal.style.display = 'flex'; } }
            function closeModal() { if (modal) { modal.style.display = 'none'; } }
            
            // فتح المودال للإضافة
            if(openBtn) {
                openBtn.addEventListener('click', function() {
                    if (form) {
                        form.action = '{{ route('admin.users.store') }}';
                        form.method = 'POST';
                        
                        // إزالة حقل _method إذا وجد
                        const methodField = form.querySelector('input[name="_method"]');
                        if (methodField) methodField.remove();
                        
                        form.reset(); 
                        
                        // ⭐️ تنظيف الحقول يدوياً للتأكد من عدم بقاء أي بيانات سابقة (Browser Cache)
                        form.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="password"]').forEach(input => input.value = '');
                        if(form.querySelector('textarea')) form.querySelector('textarea').value = '';
                        if(form.querySelector('select')) form.querySelector('select').selectedIndex = 0; // افتراضياً الخيار الأول
                        
                        // إلغاء تحديد كل الـ checkboxes عند الإضافة
                        if(form.querySelectorAll('input[name="role_names[]"]')) {
                            form.querySelectorAll('input[name="role_names[]"]').forEach(checkbox => {
                                checkbox.checked = false;
                            });
                        }

                        if (modalTitle) modalTitle.textContent = 'إضافة مستخدم جديد';
                    }
                    openModal();
                });
            }

            if(closeBtn) closeBtn.addEventListener('click', closeModal);
            if(cancelBtn) cancelBtn.addEventListener('click', closeModal);

            if (modal) {
                modal.addEventListener('click', function(event) {
                    if (event.target === modal) { closeModal(); }
                });
            }

            // ===============================================
            // ⭐️ كود معالجة التعديل (Edit Logic) ⭐️
            // ===============================================
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    try {
                        // 1. قراءة البيانات من الـ JSON الصحيح
                        const userData = JSON.parse(this.getAttribute('data-user-data'));
                        
                        // 2. تغيير العنوان
                        if (modalTitle) modalTitle.textContent = 'تعديل بيانات: ' + userData.Fname + ' ' + userData.Lname;
                        
                        // 3. تحديث مسار الفورم
                        if (form) {
                            form.action = '{{ url('admin/users') }}/' + userData.id;
                            // الفورم يبقى POST ولكن نضيف حقل مخفي PUT
                            
                            let methodField = form.querySelector('input[name="_method"]');
                            if (!methodField) {
                                methodField = document.createElement('input');
                                methodField.setAttribute('type', 'hidden');
                                methodField.setAttribute('name', '_method');
                                form.appendChild(methodField);
                            }
                            methodField.value = 'PUT';
                            
                            // 4. تعبئة الحقول (تأكد أن الـ ID لهذه الحقول موجود في _user_form.blade.php)
                            if(form.querySelector('#Fname')) form.querySelector('#Fname').value = userData.Fname;
                            if(form.querySelector('#Lname')) form.querySelector('#Lname').value = userData.Lname;
                            if(form.querySelector('#email')) form.querySelector('#email').value = userData.email;
                            if(form.querySelector('#phone')) form.querySelector('#phone').value = userData.phone;
                            
                            // تعبئة الأدوار المتعددة (Checkboxes)
                            // 1. إلغاء تحديد الكل أولاً
                            if(form.querySelectorAll('input[name="role_names[]"]')) {
                                form.querySelectorAll('input[name="role_names[]"]').forEach(checkbox => {
                                    checkbox.checked = false;
                                });
                                
                                // 2. تحديد الأدوار الموجودة
                                if (userData.role_names && Array.isArray(userData.role_names)) {
                                    userData.role_names.forEach(roleName => {
                                        // البحث عن الـ checkbox الذي يحمل القيمة المطابقة
                                        const checkbox = form.querySelector(`input[name="role_names[]"][value="${roleName}"]`);
                                        if (checkbox) {
                                            checkbox.checked = true;
                                        }
                                    });
                                }
                            }

                            if(form.querySelector('#account_state')) form.querySelector('#account_state').value = userData.account_state;
                            
                            openModal();
                        }
                    } catch (error) {
                        console.error('Error parsing user data:', error);
                        alert('حدث خطأ أثناء تحميل البيانات.');
                    }
                });
            });

            // كود الحذف
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    if(confirm('هل أنت متأكد من حذف هذا المستخدم؟')) {
                        const deleteForm = document.getElementById('delete-form');
                        deleteForm.action = '{{ url('admin/users') }}/' + userId;
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@endpush