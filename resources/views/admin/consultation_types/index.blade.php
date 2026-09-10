@extends('layouts.admin_app')

@section('title', 'أنواع الاستشارات')

@section('content')
    <div>
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 class="page-title" data-i18n="consultationTypes">أنواع الاستشارات</h1>
            <div>
                <button class="btn-primary" id="openAddTypeModal">
                    <i class="fas fa-plus"></i>
                    <span data-i18n="addNewType">أضف نوع جديد</span>
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
                        <th style="width: 30%;">اسم النوع</th>
                        <th style="width: 20%;">السعر (YER)</th>
                        <th style="width: 20%;">المدة (أيام)</th>
                        <th class="col-actions">الإجراءات</th> 
                    </tr>
                </thead>
                <tbody>
                    @if ($types->isNotEmpty())
                        @foreach ($types as $type)
                        <tr>
                            <td>{{ $type->type_id }}</td>
                            <td style="font-weight:bold;">{{ $type->name }}</td>
                            <td>{{ number_format($type->price, 2) }}</td>
                            <td>{{ $type->duration_days }}</td>
                            <td>
                                <div class="diet-card-actions" style="justify-content: center;">
                                    <a href="#" 
                                       class="diet-action-btn edit edit-btn" 
                                       title="تعديل" 
                                       data-type-id="{{ $type->type_id }}"
                                       data-type-name="{{ $type->name }}"
                                       data-type-price="{{ $type->price }}"
                                       data-type-duration="{{ $type->duration_days }}"
                                       onclick="event.preventDefault();">
                                        ✏️
                                    </a>
                                    
                                    <button class="diet-action-btn delete delete-btn" 
                                            title="حذف" 
                                            data-type-id="{{ $type->type_id }}"
                                            style="border:none; outline:none;">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">لم يتم العثور على أنواع استشارات.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            {{ $types->links() }}
        </div>
    </div>

    {{-- Modal (Add/Edit) --}}
    <div id="addTypeModal" class="modal" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="modal-content">
            
            <div class="modal-header">
                <h2 class="modal-title">أضف نوع استشارة جديد</h2>
                <span class="close-btn" id="closeAddTypeModal">&times;</span>
            </div>

            <form action="{{ route('consultation-types.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="name">اسم النوع</label>
                    <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}" placeholder="مثال: متابعة أسبوعية">
                </div>

                <div class="form-group">
                    <label for="price">السعر (YER)</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" required value="{{ old('price') }}">
                </div>

                <div class="form-group">
                    <label for="duration_days">مدة الفعالية (بالأيام)</label>
                    <input type="number" name="duration_days" id="duration_days" class="form-control" required value="{{ old('duration_days', 7) }}">
                    <small style="color:#666;">عدد الأيام التي تظل فيها الاستشارة نشطة.</small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="cancelAddTypeModal">إلغاء</button>
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
            
            const modal = document.getElementById('addTypeModal');
            const openBtn = document.getElementById('openAddTypeModal');
            const closeBtn = document.getElementById('closeAddTypeModal');
            const cancelBtn = document.getElementById('cancelAddTypeModal');
            const form = modal ? modal.querySelector('form') : null;
            const modalTitle = modal ? modal.querySelector('.modal-title') : null;

            function openModal() { if (modal) { modal.style.display = 'flex'; } }
            function closeModal() { if (modal) { modal.style.display = 'none'; } }
            
            // Open Modal for Add
            if(openBtn) {
                openBtn.addEventListener('click', function() {
                    if (form) {
                        form.action = '{{ route('consultation-types.store') }}';
                        form.method = 'POST';
                        
                        // Remove _method field if exists
                        const methodField = form.querySelector('input[name="_method"]');
                        if (methodField) methodField.remove();
                        
                        form.reset(); 
                        if (modalTitle) modalTitle.textContent = 'أضف نوع استشارة جديد';
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
                    const typeId = this.getAttribute('data-type-id');
                    const typeName = this.getAttribute('data-type-name');
                    const typePrice = this.getAttribute('data-type-price');
                    const typeDuration = this.getAttribute('data-type-duration');
                    
                    if (modalTitle) modalTitle.textContent = 'تعديل النوع: ' + typeName;
                    
                    if (form) {
                        form.action = '{{ url('admin/consultation-types') }}/' + typeId;
                        
                        let methodField = form.querySelector('input[name="_method"]');
                        if (!methodField) {
                            methodField = document.createElement('input');
                            methodField.setAttribute('type', 'hidden');
                            methodField.setAttribute('name', '_method');
                            form.appendChild(methodField);
                        }
                        methodField.value = 'PUT';
                        
                        if(form.querySelector('#name')) form.querySelector('#name').value = typeName;
                        if(form.querySelector('#price')) form.querySelector('#price').value = typePrice;
                        if(form.querySelector('#duration_days')) form.querySelector('#duration_days').value = typeDuration;

                        openModal();
                    }
                });
            });

            // Delete Logic
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const typeId = this.getAttribute('data-type-id');
                    if(confirm('هل أنت متأكد أنك تريد حذف هذا النوع؟')) {
                        const deleteForm = document.getElementById('delete-form');
                        deleteForm.action = '{{ url('admin/consultation-types') }}/' + typeId;
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
