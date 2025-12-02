@extends('layouts.admin_app')

@section('title', 'User Management')

@section('content')
    <div>
        <div class="page-header">
            <h1 class="page-title" data-i18n="userManagement">Diet Client Management</h1>
            <button class="btn-primary" id="openAddUserModal">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="addNewClient">Add New Client</span>
            </button>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Error!</strong> Please check the data:
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
                        <th style="width: 20%;">اسم العميل</th>
                        <th style="width: 25%;">البريد الإلكتروني</th>
                        <th style="width: 15%;">الدور / الحالة</th> 
                        <th class="col-actions">الإجراءات</th> 
                    </tr>
                </thead>
                <tbody>
                    @if ($users->isNotEmpty())
                        @foreach ($users as $user)
                        <tr>
                            {{-- 1. Image --}}
                            <td>
                                @if ($user->photo_url)
                                    <img src="{{ asset('storage/' . $user->photo_url) }}" alt="{{ $user->Fname }}" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                                @else
                                    <div style="width:40px; height:40px; background: #ccc; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white;">{{ substr($user->Fname, 0, 1) }}</div>
                                @endif
                            </td>
                            
                            {{-- 2. Name --}}
                            <td>
                                <a href="{{ url('admin/users/profile/' . $user->user_id) }}" style="font-weight:bold; color:#333; text-decoration:none;">{{ $user->Fname }} {{ $user->Lname }}</a>
                            </td>

                            {{-- 3. Email --}}
                            <td>
                                {{ $user->email }}
                            </td>
                            
                            {{-- 4. Role --}}
                            <td>
                                @if ($user->roles->isNotEmpty())
                                    @foreach($user->roles as $role)
                                        <span class="diet-tag diet-{{ strtolower($role->name) }}">{{ $role->name }}</span>
                                    @endforeach
                                @else
                                    <span class="diet-tag diet-client">Client</span>
                                @endif
                            </td>
                            
                            {{-- 5. Actions --}}
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ url('admin/messages?user=' . $user->user_id) }}" class="action-btn message-btn" title="Message Client"><i class="fas fa-envelope"></i></a>
                                   
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
                                       data-user-data="{{ json_encode($userData) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="action-btn delete-btn" title="Delete Client" data-user-id="{{ $user->user_id }}"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">No users found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal (Add/Edit) --}}
    <div id="addUserModal" class="modal" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="modal-content">
            
            <div class="modal-header">
                <h2 class="modal-title">Add New User</h2>
                <span class="close-btn" id="closeAddUserModal">&times;</span>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @include('admin.partials._user_form', ['allRoles' => $allRoles ?? []]) 

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="cancelAddUserModal" style="padding: 10px 15px; border-radius: 8px; border: 1px solid #ddd; background: #f3f4f6; cursor: pointer;">Cancel</button>
                    <button type="submit" class="save-btn">Save User</button>
                </div>
            </form>

        </div>
    </div>

    {{-- Hidden Delete Form --}}
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

                        if (modalTitle) modalTitle.textContent = 'Add New User';
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
                        
                        if (modalTitle) modalTitle.textContent = 'Edit User: ' + userData.Fname + ' ' + userData.Lname;
                        
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
                    if(confirm('Are you sure you want to delete this user?')) {
                        const deleteForm = document.getElementById('delete-form');
                        deleteForm.action = '{{ url('admin/users') }}/' + userId;
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@endpush