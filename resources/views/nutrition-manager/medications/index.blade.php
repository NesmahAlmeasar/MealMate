@extends('layouts.admin_app')

@section('title', 'إدارة السجل الطبي ')

@section('content')
    <div style="padding: var(--spacing-xl);">
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl);">
            <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; color: var(--text-dark); margin: 0;" data-i18n="medicationsManagement">إدارة الادوية الطبية  </h1>
            <button class="btn btn-primary" id="openAddMedicationModal">
                <i class="fas fa-plus"></i>
                <span data-i18n="addNewMedication">أضف دواء شائع الاستخدام جديد</span>
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
                        <th style="padding: var(--spacing-md); text-align: right; border-bottom: 2px solid var(--border-color); width: 70%;">السجل الطبي / الدواء</th>
                        <th style="padding: var(--spacing-md); text-align: center; border-bottom: 2px solid var(--border-color);">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($medications->isNotEmpty())
                        @foreach ($medications as $med)
                        <tr>
                            <td style="padding: var(--spacing-md); border-bottom: 1px solid var(--bg-gray-dark);">{{ $med->medical_record }}</td>
                            <td style="padding: var(--spacing-md); text-align: center; border-bottom: 1px solid var(--bg-gray-dark);">
                                <div class="action-buttons" style="justify-content: center;">
                                    @php
                                        $medicationData = [
                                            "id" => $med->medical_record_id,
                                            "medical_record" => $med->medical_record,
                                        ];
                                    @endphp

                                    <a href="#" 
                                       class="action-btn edit edit-btn" 
                                       title="تعديل السجل" 
                                       data-medication-id="{{ $med->medical_record_id }}"
                                       data-medication-data="{{ json_encode($medicationData) }}">
                                        ✏️
                                    </a>
                                    <button class="action-btn delete delete-btn" title="حذف السجل" data-medication-id="{{ $med->medical_record_id }}">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="2" style="text-align: center; padding: 20px; color: var(--text-light);">لم يتم العثور على ادوية شائعة مستخدمة .</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $medications->links('vendor.pagination.custom') }}
        </div>
    </div>

    {{-- Modal (Add/Edit) --}}
    <div id="addMedicationModal" class="modal" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="modal-content">
            
            <div class="modal-header">
                <h2 class="modal-title">إضافة دواء شائع الاستخدام جديد</h2>
                <span class="close-btn" id="closeAddMedicationModal">&times;</span>
            </div>

            <form action="{{ route('nutrition-manager.medications.store') }}" method="POST">
                @csrf
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="medical_record">السجل الطبي / الدواء <span style="color: var(--red-accent);">*</span></label>
                        <input type="text" id="medical_record" name="medical_record" required placeholder="مثال: أسبرين 100 مجم يومياً">
                        @error('medical_record')
                            <span style="color: var(--red-accent); font-size: 0.85em;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelAddMedicationModal">إلغاء</button>
                    <button type="submit" class="btn btn-primary save-btn">حفظ السجل</button>
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
            
            const modal = document.getElementById('addMedicationModal');
            const openBtn = document.getElementById('openAddMedicationModal');
            const closeBtn = document.getElementById('closeAddMedicationModal');
            const cancelBtn = document.getElementById('cancelAddMedicationModal');
            const form = modal ? modal.querySelector('form') : null;
            const modalTitle = modal ? modal.querySelector('.modal-title') : null;

            function openModal() { if (modal) { modal.style.display = 'flex'; } }
            function closeModal() { if (modal) { modal.style.display = 'none'; } }
            
            // Open Modal for Add
            if(openBtn) {
                openBtn.addEventListener('click', function() {
                    if (form) {
                        form.action = '{{ route('nutrition-manager.medications.store') }}';
                        form.method = 'POST';
                        
                        // Remove _method field if exists
                        const methodField = form.querySelector('input[name="_method"]');
                        if (methodField) methodField.remove();
                        
                        form.reset(); 
                        
                        // Clear fields manually
                        form.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
                        
                        if (modalTitle) modalTitle.textContent = 'إضافة دواء شائع الاستخدام جديد';
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
                        const medicationData = JSON.parse(this.getAttribute('data-medication-data'));
                        
                        if (modalTitle) modalTitle.textContent = 'تعديل السجل: ' + medicationData.medical_record;
                        
                        if (form) {
                            form.action = '{{ url('nutrition-manager/medications') }}/' + medicationData.id;
                            
                            let methodField = form.querySelector('input[name="_method"]');
                            if (!methodField) {
                                methodField = document.createElement('input');
                                methodField.setAttribute('type', 'hidden');
                                methodField.setAttribute('name', '_method');
                                form.appendChild(methodField);
                            }
                            methodField.value = 'PUT';
                            
                            if(form.querySelector('#medical_record')) form.querySelector('#medical_record').value = medicationData.medical_record || '';
                            
                            openModal();
                        }
                    } catch (error) {
                        console.error('Error parsing medication data:', error);
                        alert('خطأ في تحميل البيانات.');
                    }
                });
            });

            // Delete Logic
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const medicationId = this.getAttribute('data-medication-id');
                    if(confirm('هل أنت متأكد أنك تريد حذف هذا السجل؟')) {
                        const deleteForm = document.getElementById('delete-form');
                        deleteForm.action = '{{ url('nutrition-manager/medications') }}/' + medicationId;
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
