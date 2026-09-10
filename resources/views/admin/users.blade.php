@extends('layouts.admin_app')

@section('title', 'إدارة المستخدمين')

@section('content')
    <div>
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 class="page-title" data-i18n="userManagement"> إدارة المستخدمين</h1>
            <div style="display: flex; gap: 10px;">
                <!-- Role Filter Form -->
                <form method="GET" action="{{ url('admin/users') }}" style="display: flex; align-items: center; gap: 10px;">
                    <select name="role" class="form-control" onchange="this.form.submit()" style="padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border-color); font-family: 'Cairo';">
                        <option value="">كل المستخدمين</option>
                        @foreach($allRoles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <button class="btn-primary" id="openAddUserModal">
                    <i class="fas fa-user-plus"></i>
                    <span data-i18n="addNewClient">أضف مستخدم جديد</span>
                </button>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>خطأ!</strong> يرجى التحقق من البيانات:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <div class="table-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="col-img">صورة</th>
                        <th style="width: 20%;">اسم المستخدم</th>
                        <th style="width: 25%;">البريد الإلكتروني</th>
                        <th style="width: 15%;">الدور / الحالة</th> 
                        <th class="col-actions">الإجراءات</th> 
                    </tr>
                </thead>
                <tbody>
                    @if ($users->isNotEmpty())
                        @foreach ($users as $user)
                        <tr>
                                    @php
                                        $userData = [
                                            "id" => $user->user_id,
                                            "Fname" => $user->Fname,
                                            "Lname" => $user->Lname,
                                            "email" => $user->email,
                                            "phone" => $user->phone,
                                            "role_names" => $user->roles->pluck('name')->toArray(),
                                            "account_state" => $user->account_state,
                                            "photo_url" => $user->photo_url,
                                        ];
                                    @endphp
                            {{-- 1. Image --}}
                            <td>
                                <a href="#" class="view-profile-btn" data-user-data="{{ json_encode($userData) }}" onclick="event.preventDefault();">
                                    @if ($user->photo_url)
                                        <img src="{{ asset('storage/' . $user->photo_url) }}" alt="{{ $user->Fname }}" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                                    @else
                                        <div style="width:40px; height:40px; background: #ccc; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white;">{{ substr($user->Fname, 0, 1) }}</div>
                                    @endif
                                </a>
                            </td>
                            
                            {{-- 2. Name --}}
                            <td>
                                <a href="#" class="view-profile-btn" data-user-data="{{ json_encode($userData) }}" style="font-weight:bold; color:#333; text-decoration:none;" onclick="event.preventDefault();">{{ $user->Fname }} {{ $user->Lname }}</a>
                            </td>

                            {{-- 3. Email --}}
                            <td>
                                {{ $user->email }}
                            </td>
                            
                            {{-- 4. Role --}}
                            <td>
                                @if ($user->roles->isNotEmpty())
                                    @foreach($user->roles as $role)
                                        @php
                                            $roleLower = strtolower($role->name);
                                            $icon = match($roleLower) {
                                                'admin' => '🤵🏻',
                                                'specialist' => '👨‍⚕️',
                                                'Restaurant Manager' => '🧑‍🍳',
                                                'nutrition manager' => '🥗',
                                                default => '👤',
                                            };
                                        @endphp
                                        <span class="role-icon" title="{{ $role->name }}" style="font-size: 1.5em; cursor: help; margin: 0 2px;">
                                            {{ $icon }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="role-icon" title="Client" style="font-size: 1.5em; cursor: help;">👤</span>
                                @endif
                            </td>
                            
                            {{-- 5. Actions --}}
                            <td>
                                <div class="diet-card-actions" style="justify-content: center;">
                                    <a href="{{ url('admin/messages?user=' . $user->user_id) }}" class="diet-action-btn message" title="Message Client">✉️</a>
                                   


                                    <a href="#" 
                                       class="diet-action-btn edit edit-btn" 
                                       title="Edit Client Data" 
                                       data-user-id="{{ $user->user_id }}"
                                       data-user-data="{{ json_encode($userData) }}"
                                       onclick="event.preventDefault();">
                                        ✏️
                                    </a>
                                    
                                    <button class="diet-action-btn delete delete-btn" 
                                            title="Delete Client" 
                                            data-user-id="{{ $user->user_id }}"
                                            style="border:none; outline:none;">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">لم يتم العثور على مستخدمين.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Modal (Add/Edit) --}}
    <div id="addUserModal" class="modal" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="modal-content">
            
            <div class="modal-header">
                <h2 class="modal-title">إضافة مستخدم جديد</h2>
                <span class="close-btn" id="closeAddUserModal">&times;</span>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @include('admin.partials._user_form', ['allRoles' => $allRoles ?? []]) 

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="cancelAddUserModal" style="padding: 10px 15px; border-radius: 8px; border: 1px solid #ddd; background: #f3f4f6; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="save-btn">حفظ المستخدم</button>
                </div>
            </form>

        </div>
    </div>

    {{-- Hidden Delete Form --}}
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE') 
    </form>

    {{-- User Profile Modal --}}
    <div id="viewUserModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 400px; text-align: center; border-radius: 15px; padding: 30px;">
            <span class="close-btn" id="closeViewUserModal" style="position: absolute; left: 20px; top: 20px;">&times;</span>
            
            <div style="margin-bottom: 20px;">
                <img id="view-profile-image" src="" alt="Profile" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid var(--olive-light);">
            </div>
            
            <h2 id="view-profile-name" style="margin-bottom: 5px; color: var(--text-dark);">User Name</h2>
            <p id="view-profile-role" style="color: var(--text-light); margin-bottom: 20px; font-weight: bold;"></p>
            
            <div style="text-align: right; background: #f9fafb; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <div style="margin-bottom: 10px;">
                    <i class="fas fa-envelope" style="color: var(--olive-medium); margin-left: 10px;"></i>
                    <span id="view-profile-email">email@example.com</span>
                </div>
                <div style="margin-bottom: 10px;">
                    <i class="fas fa-phone" style="color: var(--olive-medium); margin-left: 10px;"></i>
                    <span id="view-profile-phone">+123456789</span>
                </div>
                <div>
                     <i class="fas fa-toggle-on" style="color: var(--olive-medium); margin-left: 10px;"></i>
                     <span id="view-profile-status">Active</span>
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: center;">
                 <a href="#" id="view-profile-full-link" class="btn-primary" style="text-decoration: none; font-size: 14px;">
                    الملف الشخصي الكامل
                 </a>
                 <a href="#" id="view-profile-chat-link" class="btn-secondary" style="text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 5px;">
                    <i class="fas fa-comment"></i> محادثة
                 </a>
            </div>
        </div>
    </div>

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
            
            // Open Modal for Add
            if(openBtn) {
                openBtn.addEventListener('click', function() {
                    if (form) {
                        form.action = '{{ route('admin.users.store') }}';
                        form.method = 'POST';
                        
                        // Remove _method field if exists
                        const methodField = form.querySelector('input[name="_method"]');
                        if (methodField) methodField.remove();
                        
                        form.reset(); 
                        
                        // Clear fields manually to avoid browser cache issues
                        form.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="password"]').forEach(input => input.value = '');
                        if(form.querySelector('textarea')) form.querySelector('textarea').value = '';
                        if(form.querySelector('select')) form.querySelector('select').selectedIndex = 0;
                        
                        // Uncheck all checkboxes
                        if(form.querySelectorAll('input[name="role_names[]"]')) {
                            form.querySelectorAll('input[name="role_names[]"]').forEach(checkbox => {
                                checkbox.checked = false;
                            });
                        }

                        if (modalTitle) modalTitle.textContent = 'إضافة مستخدم جديد';

                        // Reset Image Preview
                        const imgPreview = form.querySelector('#modal-preview-image');
                        if (imgPreview) {
                            imgPreview.style.display = 'none';
                            imgPreview.src = '';
                        }
                        
                        // Hide Password Hint for Add
                        const passHint = document.getElementById('password-hint');
                        if(passHint) passHint.style.display = 'none';
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
            // ⭐️ Edit Logic ⭐️
            // ===============================================
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    try {
                        const userData = JSON.parse(this.getAttribute('data-user-data'));
                        
                        if (modalTitle) modalTitle.textContent = 'تعديل المستخدم: ' + userData.Fname + ' ' + userData.Lname;
                        
                        if (form) {
                            form.action = '{{ url('admin/users') }}/' + userData.id;
                            
                            let methodField = form.querySelector('input[name="_method"]');
                            if (!methodField) {
                                methodField = document.createElement('input');
                                methodField.setAttribute('type', 'hidden');
                                methodField.setAttribute('name', '_method');
                                form.appendChild(methodField);
                            }
                            methodField.value = 'PUT';
                            
                            if(form.querySelector('#Fname')) form.querySelector('#Fname').value = userData.Fname;
                            if(form.querySelector('#Lname')) form.querySelector('#Lname').value = userData.Lname;
                            if(form.querySelector('#email')) form.querySelector('#email').value = userData.email;
                            if(form.querySelector('#phone')) form.querySelector('#phone').value = userData.phone;
                            
                            // Handle Roles Checkboxes
                            if(form.querySelectorAll('input[name="role_names[]"]')) {
                                form.querySelectorAll('input[name="role_names[]"]').forEach(checkbox => {
                                    checkbox.checked = false;
                                });
                                
                                if (userData.role_names && Array.isArray(userData.role_names)) {
                                    userData.role_names.forEach(roleName => {
                                        const checkbox = form.querySelector(`input[name="role_names[]"][value="${roleName}"]`);
                                        if (checkbox) {
                                            checkbox.checked = true;
                                        }
                                    });
                                }
                            }

                            if(form.querySelector('#account_state')) form.querySelector('#account_state').value = userData.account_state;
                            
                            
                            // Image Preview Logic
                            const imgPreview = form.querySelector('#modal-preview-image');
                            if (imgPreview) {
                                if (userData.photo_url) {
                                    imgPreview.src = '{{ asset("storage") }}/' + userData.photo_url;
                                    imgPreview.style.display = 'block';
                                } else {
                                    imgPreview.style.display = 'none';
                                }
                            }
                            
                            // Show Password Hint for Edit
                            const passHint = document.getElementById('password-hint');
                            if(passHint) passHint.style.display = 'inline';

                            openModal();
                        }
                    } catch (error) {
                        console.error('Error parsing user data:', error);
                        alert('Error loading data.');
                    }
                });
            });

            // Delete Logic
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    if(confirm('هل أنت متأكد أنك تريد حذف هذا المستخدم؟')) {
                        const deleteForm = document.getElementById('delete-form');
                        deleteForm.action = '{{ url('admin/users') }}/' + userId;
                        deleteForm.submit();
                    }
                });
            });

            // ===============================================
            // ⭐️ View Profile Modal Logic ⭐️
            // ===============================================
            const viewModal = document.getElementById('viewUserModal');
            const closeViewBtn = document.getElementById('closeViewUserModal');
            
            if(viewModal && closeViewBtn) {
                 closeViewBtn.addEventListener('click', function() {
                    viewModal.style.display = 'none';
                });
                
                viewModal.addEventListener('click', function(event) {
                    if (event.target === viewModal) { viewModal.style.display = 'none'; }
                });
            }

            document.querySelectorAll('.view-profile-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                   e.preventDefault();
                   try {
                       const userData = JSON.parse(this.getAttribute('data-user-data'));
                       
                       // Populate Modal
                       document.getElementById('view-profile-name').textContent = userData.Fname + ' ' + (userData.Lname || '');
                       document.getElementById('view-profile-email').textContent = userData.email;
                       document.getElementById('view-profile-phone').textContent = userData.phone || 'N/A';
                       document.getElementById('view-profile-role').textContent = userData.role_names.join(', ');
                       
                       const statusSpan = document.getElementById('view-profile-status');
                       statusSpan.textContent = userData.account_state === 'Active' ? 'نشط' : 'غير نشط';
                       statusSpan.style.color = userData.account_state === 'Active' ? 'green' : 'red';
                       
                       const img = document.getElementById('view-profile-image');
                       if (userData.photo_url) {
                           img.src = '{{ asset("storage") }}/' + userData.photo_url;
                           img.style.display = 'inline-block';
                       } else {
                           // Placeholder or hide
                           img.src = '{{ asset("images/mealmate.png") }}'; // Fallback
                       }

                       // Links
                       document.getElementById('view-profile-full-link').href = '{{ url("admin/users/profile") }}/' + userData.id;
                       document.getElementById('view-profile-chat-link').href = '{{ url("admin/messages") }}?user=' + userData.id;

                       if(viewModal) viewModal.style.display = 'flex';

                   } catch(error) {
                       console.error('Error parsing profile data', error);
                   }
                });
            });
        });
    </script>
@endpush