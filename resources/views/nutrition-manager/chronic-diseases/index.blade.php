@extends('layouts.admin_app')

@section('title', 'إدارة الأمراض المزمنة')

@section('content')
    <div style="padding: var(--spacing-xl);">
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl);">
            <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; color: var(--text-dark); margin: 0;" data-i18n="chronicDiseasesManagement">إدارة الأمراض المزمنة</h1>
            <button class="btn btn-primary" id="openAddDiseaseModal">
                <i class="fas fa-plus"></i>
                <span data-i18n="addNewDisease">أضف مرض جديد</span>
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
                <strong>خطأ!</strong> يرجى التحقق من البيانات:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card" style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: var(--spacing-md); text-align: right; border-bottom: 2px solid var(--border-color); width: 70%;">اسم المرض المزمن</th>
                        <th style="padding: var(--spacing-md); text-align: center; border-bottom: 2px solid var(--border-color);">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($diseases->isNotEmpty())
                        @foreach ($diseases as $disease)
                        <tr>
                            <td style="padding: var(--spacing-md); border-bottom: 1px solid var(--bg-gray-dark);">{{ $disease->chronic_diseases }}</td>
                            <td style="padding: var(--spacing-md); text-align: center; border-bottom: 1px solid var(--bg-gray-dark);">
                                <div class="action-buttons" style="justify-content: center;">
                                    @php
                                        $diseaseData = [
                                            "id" => $disease->chronic_diseases_id,
                                            "chronic_diseases" => $disease->chronic_diseases,
                                        ];
                                    @endphp

                                    <a href="#" 
                                       class="action-btn edit edit-btn" 
                                       title="تعديل المرض" 
                                       data-disease-id="{{ $disease->chronic_diseases_id }}"
                                       data-disease-data="{{ json_encode($diseaseData) }}">
                                        ✏️
                                    </a>
                                    <button class="action-btn delete delete-btn" title="حذف المرض" data-disease-id="{{ $disease->chronic_diseases_id }}">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="2" style="text-align: center; padding: 20px; color: var(--text-light);">لم يتم العثور على أمراض مزمنة.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $diseases->links('vendor.pagination.custom') }}
        </div>
    </div>

    {{-- Modal (Add/Edit) --}}
    <div id="addDiseaseModal" class="modal" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="modal-content">
            
            <div class="modal-header">
                <h2 class="modal-title">إضافة مرض مزمن جديد</h2>
                <span class="close-btn" id="closeAddDiseaseModal">&times;</span>
            </div>

            <form action="{{ route('nutrition-manager.chronic-diseases.store') }}" method="POST">
                @csrf
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="chronic_diseases">اسم المرض المزمن <span style="color: var(--red-accent);">*</span></label>
                        <input type="text" id="chronic_diseases" name="chronic_diseases" required placeholder="مثال: السكري، ضغط الدم، إلخ...">
                        @error('chronic_diseases')
                            <span style="color: var(--red-accent); font-size: 0.85em;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelAddDiseaseModal">إلغاء</button>
                    <button type="submit" class="btn btn-primary save-btn">حفظ المرض</button>
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
            
            const modal = document.getElementById('addDiseaseModal');
            const openBtn = document.getElementById('openAddDiseaseModal');
            const closeBtn = document.getElementById('closeAddDiseaseModal');
            const cancelBtn = document.getElementById('cancelAddDiseaseModal');
            const form = modal ? modal.querySelector('form') : null;
            const modalTitle = modal ? modal.querySelector('.modal-title') : null;

            function openModal() { if (modal) { modal.style.display = 'flex'; } }
            function closeModal() { if (modal) { modal.style.display = 'none'; } }
            
            // Open Modal for Add
            if(openBtn) {
                openBtn.addEventListener('click', function() {
                    if (form) {
                        form.action = '{{ route('nutrition-manager.chronic-diseases.store') }}';
                        form.method = 'POST';
                        
                        // Remove _method field if exists
                        const methodField = form.querySelector('input[name="_method"]');
                        if (methodField) methodField.remove();
                        
                        form.reset(); 
                        
                        // Clear fields manually
                        form.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
                        
                        if (modalTitle) modalTitle.textContent = 'إضافة مرض مزمن جديد';
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
                    
                    try {
                        const diseaseData = JSON.parse(this.getAttribute('data-disease-data'));
                        
                        if (modalTitle) modalTitle.textContent = 'تعديل المرض: ' + diseaseData.chronic_diseases;
                        
                        if (form) {
                            form.action = '{{ url('nutrition-manager/chronic-diseases') }}/' + diseaseData.id;
                            
                            let methodField = form.querySelector('input[name="_method"]');
                            if (!methodField) {
                                methodField = document.createElement('input');
                                methodField.setAttribute('type', 'hidden');
                                methodField.setAttribute('name', '_method');
                                form.appendChild(methodField);
                            }
                            methodField.value = 'PUT';
                            
                            if(form.querySelector('#chronic_diseases')) form.querySelector('#chronic_diseases').value = diseaseData.chronic_diseases || '';
                            
                            openModal();
                        }
                    } catch (error) {
                        console.error('Error parsing disease data:', error);
                        alert('خطأ في تحميل البيانات.');
                    }
                });
            });

            // Delete Logic
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const diseaseId = this.getAttribute('data-disease-id');
                    if(confirm('هل أنت متأكد أنك تريد حذف هذا المرض؟')) {
                        const deleteForm = document.getElementById('delete-form');
                        deleteForm.action = '{{ url('nutrition-manager/chronic-diseases') }}/' + diseaseId;
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
