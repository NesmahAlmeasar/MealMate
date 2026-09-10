@extends('layouts.admin_app')

@section('title', 'إدارة الأدوار')

@section('content')
    <div>
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 class="page-title" data-i18n="rolesManagement">إدارة الأدوار</h1>
            <div>
                <button class="btn-primary" id="openAddRoleModal">
                    <i class="fas fa-plus"></i>
                    <span data-i18n="addNewRole">أضف دور جديد</span>
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
                        <th style="width: 10%;">المعرف</th>
                        <th style="width: 25%;">اسم الدور</th>
                        <th style="width: 50%;">الوصف</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($roles->isNotEmpty())
                        @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->role_id }}</td>
                            <td style="font-weight:bold;">{{ $role->name }}</td>
                            <td>{{ $role->description }}</td>
                           
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="4" style="text-align: center; padding: 20px;">لم يتم العثور على أداور.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            {{ $roles->links() }}
        </div>
    </div>

    {{-- Modal (Add/Edit) --}}
    <div id="addRoleModal" class="modal" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="modal-content">
            
            <div class="modal-header">
                <h2 class="modal-title">أضف دور جديد</h2>
                <span class="close-btn" id="closeAddRoleModal">&times;</span>
            </div>

            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="name">اسم الدور</label>
                    <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                </div>

                <div class="form-group">
                    <label for="description">الوصف</label>
                    <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="cancelAddRoleModal">إلغاء</button>
                    <button type="submit" class="save-btn">حفظ</button>
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
            
            const modal = document.getElementById('addRoleModal');
            const openBtn = document.getElementById('openAddRoleModal');
            const closeBtn = document.getElementById('closeAddRoleModal');
            const cancelBtn = document.getElementById('cancelAddRoleModal');
            const form = modal ? modal.querySelector('form') : null;
            const modalTitle = modal ? modal.querySelector('.modal-title') : null;

            function openModal() { if (modal) { modal.style.display = 'flex'; } }
            function closeModal() { if (modal) { modal.style.display = 'none'; } }
            
            // Open Modal for Add
            if(openBtn) {
                openBtn.addEventListener('click', function() {
                    if (form) {
                        form.action = '{{ route('roles.store') }}';
                        form.method = 'POST';
                        
                        // Remove _method field if exists
                        const methodField = form.querySelector('input[name="_method"]');
                        if (methodField) methodField.remove();
                        
                        form.reset(); 
                        if (modalTitle) modalTitle.textContent = 'أضف دور جديد';
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

            // Edit Logic
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const roleId = this.getAttribute('data-role-id');
                    const roleName = this.getAttribute('data-role-name');
                    const roleDesc = this.getAttribute('data-role-description');
                    
                    if (modalTitle) modalTitle.textContent = 'تعديل الدور: ' + roleName;
                    
                    if (form) {
                        form.action = '{{ url('admin/roles') }}/' + roleId;
                        
                        let methodField = form.querySelector('input[name="_method"]');
                        if (!methodField) {
                            methodField = document.createElement('input');
                            methodField.setAttribute('type', 'hidden');
                            methodField.setAttribute('name', '_method');
                            form.appendChild(methodField);
                        }
                        methodField.value = 'PUT';
                        
                        if(form.querySelector('#name')) form.querySelector('#name').value = roleName;
                        if(form.querySelector('#description')) form.querySelector('#description').value = roleDesc;

                        openModal();
                    }
                });
            });

            // Delete Logic
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const roleId = this.getAttribute('data-role-id');
                    if(confirm('هل أنت متأكد أنك تريد حذف هذا الدور؟')) {
                        const deleteForm = document.getElementById('delete-form');
                        deleteForm.action = '{{ url('admin/roles') }}/' + roleId;
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
