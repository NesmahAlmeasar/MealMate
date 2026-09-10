{{-- في ملف: resources/views/admin/partials/_user_form.blade.php --}}

@php
    // تحديد ما إذا كنا في وضع التعديل (لتحديد القيم الافتراضية)
    $isEdit = isset($user) && $user->user_id;
    // تحديد الدور الحالي من العلاقة (ضروري لتعبئة قائمة الأدوار)
    $currentRoleName = $isEdit && $user->roles->first() ? $user->roles->first()->name : old('role_name', 'Client');
    // تحديد حالة الحساب الحالية
    $currentState = $isEdit ? $user->account_state : old('account_state', 'Active');
@endphp

{{-- 2. إضافة ملف الـ CSS الخاص بالجدول --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
    
    <style>


        /* تحسين تصميم الفورم داخل المودال */
.modal-body-container {
    padding: 10px;
}

/* استخدام Grid لتقسيم الحقول إلى عمودين */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr; /* عمودين متساويين */
    gap: 20px; /* مسافة بين الحقول */
}

/* جعل بعض الحقول تأخذ العرض الكامل (مثل البريد والصورة) */
.form-group.full-width {
    grid-column: span 2;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px; /* مسافة بين العنوان والحقل */
}

.form-group label {
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    margin-bottom: 2px;
}

/* تنسيق حقول الإدخال */
.form-group input,
.form-group select {
    padding: 10px 12px;
    border: 1px solid #D1D5DB;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
    background-color: #fff;
    width: 100%; /* ضمان ملء العرض المتاح */
    box-sizing: border-box; /* حساب الحواف داخل العرض */
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #6B8E23; /* لون الزيتوني عند التركيز */
    box-shadow: 0 0 0 3px rgba(107, 142, 35, 0.1);
}

/* تنسيق خاص لحقل رفع الملفات */
.file-upload-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px dashed #D1D5DB;
    padding: 15px;
    border-radius: 8px;
    background-color: #F9FAFB;
}

.current-photo-preview {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* تحسين تجاوب الموبايل */
@media (max-width: 600px) {
    .form-grid {
        grid-template-columns: 1fr; /* عمود واحد في الشاشات الصغيرة */
    }
    .form-group.full-width {
        grid-column: span 1;
    }
}
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
        .diet-tag.diet-nutrition { background-color: #FEF3C7; color: #D97706; }
        .diet-tag.diet-manager { background-color: #FEF3C7; color: #D97706; }

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
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* تحسين تجاوب الموبايل */
@media (max-width: 600px) {
    .form-grid {
        grid-template-columns: 1fr; /* عمود واحد في الشاشات الصغيرة */
    }
    .form-group.full-width {
        grid-column: span 1;
    }
}
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

<div class="modal-body">
    <div class="form-grid">
        
        {{-- الحقل: الاسم الأول --}}
        <div class="form-group">
            <label for="Fname">الاسم الأول (First Name)</label>
            <input type="text" id="Fname" name="Fname" value="{{ old('Fname', $isEdit ? $user->Fname : '') }}" required 
                   class="@error('Fname') is-invalid @enderror">
            @error('Fname')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- الحقل: الاسم الأخير --}}
        <div class="form-group">
            <label for="Lname">الاسم الأخير (Last Name)</label>
            <input type="text" id="Lname" name="Lname" value="{{ old('Lname', $isEdit ? $user->Lname : '') }}" required 
                   class="@error('Lname') is-invalid @enderror">
            @error('Lname')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- الحقل: Email --}}
        <div class="form-group">
            <label for="email">البريد الإلكتروني (Email)</label>
            <input type="email" id="email" name="email" value="{{ old('email', $isEdit ? $user->email : '') }}" required
                   class="@error('email') is-invalid @enderror">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- الحقل: Phone --}}
        <div class="form-group">
            <label for="phone">الهاتف (Phone)</label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone', $isEdit ? $user->phone : '') }}"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   class="@error('phone') is-invalid @enderror">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- الحقل: Password --}}
        <div class="form-group">
            <label for="password"> كلمة المرور <small id="password-hint" style="display: none; color: #666; font-size: 0.85em;">(اتركها فارغة إذا لم ترد التغيير)</small></label>
            <input type="password" id="password" name="password" {{ $isEdit ? '' : 'required' }}
                   class="@error('password') is-invalid @enderror">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- الحقل: Password Confirmation --}}
        <div class="form-group">
            <label for="password_confirmation">تأكيد كلمة المرور</label>
            <input type="password" id="password_confirmation" name="password_confirmation" {{ $isEdit ? '' : 'required' }}>
        </div>

        {{-- الحقل: Role (الأدوار) - Checkboxes --}}
        <div class="form-group full-width">
            <label class="mb-2">الأدوار (Roles)</label>
            @php
                // تحديد الأدوار الحالية (مصفوفة)
                // في حالة الخطأ (validation error)، نستخدم old()، وإلا نستخدم بيانات المستخدم إذا كانت موجودة، أو الافتراضي 'Client'
                $currentRoleNames = old('role_names', $isEdit ? $user->roles->pluck('name')->toArray() : ['Client']);
            @endphp
            
            <div class="roles-checkbox-container" style="display: flex; gap: 15px; flex-wrap: wrap; background: #f9fafb; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                @foreach ($allRoles as $role)
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" 
                               class="custom-control-input role-checkbox" 
                               id="role_{{ $role->id }}" 
                               name="role_names[]" 
                               value="{{ $role->name }}"
                               {{ in_array($role->name, $currentRoleNames) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="role_{{ $role->id }}" style="cursor: pointer; margin-right: 5px;">
                            {{ $role->name }}
                            @if($role->name == 'Client') (مستفيد) @endif
                            @if($role->name == 'Admin') (مدير) @endif
                            @if($role->name == 'Specialist') (أخصائي) @endif
                            @if($role->name == 'Nutrition Manager') (مدير التغذية) @endif
                        </label>
                    </div>
                @endforeach
            </div>
            
            @error('role_names')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- الحقل: Photo --}}
        <div class="form-group full-width">
            <label for="photo">الصورة (Photo) {{ $isEdit ? '   ' : '' }}</label>
            <div class="file-upload-wrapper">
                <input type="file" id="photo" name="photo" class="form-control @error('photo') is-invalid @enderror">
                <img id="modal-preview-image" src="" alt="Current Profile" class="current-photo-preview" style="display: none; max-width: 100px; margin-top: 10px;">
            </div>
            @error('photo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- الحقل: Account State --}}
        <div class="form-group full-width">
            <label for="account_state">حالة الحساب (Account State)</label>
            <select id="account_state" name="account_state" class="@error('account_state') is-invalid @enderror">
                <option value="Active" {{ $currentState == 'Active' ? 'selected' : '' }}>نشط (Active)</option>
                <option value="Pending" {{ $currentState == 'Pending' ? 'selected' : '' }}>قيد الانتظار (Pending)</option>
                <option value="Banned" {{ $currentState == 'Banned' ? 'selected' : '' }}>محظور (Banned)</option>
            </select>
            @error('account_state')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div><style>.invalid-feedback { color: var(--red-accent, #DC2626); font-size: 0.85em; margin-top: 4px; }</style>
