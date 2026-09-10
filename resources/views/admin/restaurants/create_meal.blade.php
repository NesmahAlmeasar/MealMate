@extends('layouts.admin_app')

@section('title', 'إضافة وجبة إلى ' . $restaurant->name)

@push('styles')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }
    .form-col {
        flex: 1;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    .form-control:focus {
        border-color: #667eea;
        outline: none;
    }
    .btn-submit {
        background: rgba(107, 142, 35, 0.15);
        color: #556B2F;
        border: 2px solid #6B8E23;
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(107, 142, 35, 0.2);
    }
    .btn-submit:hover {
        background: #6B8E23;
        color: white;
        box-shadow: 0 6px 20px rgba(107, 142, 35, 0.4);
        transform: translateY(-2px);
    }
    .btn-cancel {
        display: block;
        text-align: center;
        margin-top: 15px;
        color: #666;
        text-decoration: none;
    }
    .input-group {
        display: flex;
        gap: 10px;
    }
    .btn-add-category {
        background: #28a745;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0 15px;
        cursor: pointer;
        font-weight: 600;
    }
    
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
        background-color: white;
        margin: 15% auto;
        padding: 25px;
        border-radius: 10px;
        width: 400px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .close-modal {
        cursor: pointer;
        font-size: 1.5rem;
        color: #666;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="form-container">
        <h2 style="margin-bottom: 25px; text-align: center;">إضافة وجبة إلى {{ $restaurant->name }}</h2>
        
        <form action="{{ route('admin.restaurants.meals.store', $restaurant->restaurants_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-row">
                <div class="form-col">
                    <label for="name" class="form-label">اسم الوجبة</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="form-col">
                    <label for="price" class="form-label">السعر ($)</label>
                    <input type="number" name="price" id="price" class="form-control" step="0.01" value="{{ old('price') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="category_id" class="form-label">الفئة</label>
                <div class="input-group">
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">اختر الفئة</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn-add-category" onclick="openModal()">
                        <i class="fas fa-plus"></i> جديد
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">الوصف</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="photo" class="form-label">صورة الوجبة</label>
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
            </div>

            <h4 style="margin: 20px 0 15px 0; color: #666;">المعلومات الغذائية (اختياري)</h4>
            
            <div class="form-row">
                <div class="form-col">
                    <label class="form-label">سعرات حرارية</label>
                    <input type="number" name="calories" class="form-control" step="0.1">
                </div>
                <div class="form-col">
                    <label class="form-label">بروتين (غ)</label>
                    <input type="number" name="protein_g" class="form-control" step="0.1">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <label class="form-label">دهون (غ)</label>
                    <input type="number" name="fat_g" class="form-control" step="0.1">
                </div>
                <div class="form-col">
                    <label class="form-label">كربوهيدرات (غ)</label>
                    <input type="number" name="carbs_g" class="form-control" step="0.1">
                </div>
            </div>

            <button type="submit" class="btn-submit">إضافة الوجبة</button>
            <a href="{{ route('admin.restaurants.show', $restaurant->restaurants_id) }}" class="btn-cancel">إلغاء</a>
        </form>
    </div>
</div>

<!-- Add Category Modal -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>إضافة فئة جديدة</h3>
            <span class="close-modal" onclick="closeModal()">&times;</span>
        </div>
        <div class="form-group">
            <label class="form-label">اسم الفئة</label>
            <input type="text" id="new_category_name" class="form-control" placeholder="مثال: مقبلات">
            <div id="category-error" class="error-message" style="display: none;"></div>
        </div>
        <button type="button" class="btn-submit" onclick="saveCategory()">حفظ الفئة</button>
    </div>
</div>

@push('scripts')
<script>
    const modal = document.getElementById('categoryModal');
    
    function openModal() {
        modal.style.display = 'block';
        document.getElementById('new_category_name').focus();
    }
    
    function closeModal() {
        modal.style.display = 'none';
        document.getElementById('new_category_name').value = '';
        document.getElementById('category-error').style.display = 'none';
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    function saveCategory() {
        const name = document.getElementById('new_category_name').value;
        const errorDiv = document.getElementById('category-error');
        
        if (!name) {
            errorDiv.textContent = 'اسم الفئة مطلوب';
            errorDiv.style.display = 'block';
            return;
        }

        fetch('{{ route("admin.categories.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ category_name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new option to select and select it
                const select = document.getElementById('category_id');
                const option = new Option(data.category.category_name, data.category.category_id);
                select.add(option, 0); // Add to top
                select.value = data.category.category_id;
                
                closeModal();
            } else {
                errorDiv.textContent = data.message || 'خطأ في إنشاء الفئة';
                errorDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorDiv.textContent = 'حدث خطأ ما';
            errorDiv.style.display = 'block';
        });
    }
</script>
@endpush
@endsection
