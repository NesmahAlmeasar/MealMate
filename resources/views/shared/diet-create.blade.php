@extends('layouts.admin_app')

@section('title', 'إضافة حمية جديدة')

@section('content')
<div>
    <div class="page-header">
        <h1 class="page-title">إضافة حمية جديدة</h1>
        {{-- <p style="color: var(--text-medium);">قم بإنشاء خطة حمية شاملة</p> --}}
    </div>

    {{-- Alerts --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>يرجى تصحيح الأخطاء التالية:</strong>
            <ul style="margin-top: 5px; padding-right: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card form-container">
        <form action="{{ route('shared.diets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                {{-- Name --}}
                <div class="form-group">
                    <label for="name" class="form-label">اسم الحمية <span style="color: red;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name') <div class="error-message" style="color: var(--red-accent);">{{ $message }}</div> @enderror
                </div>

                {{-- Photo --}}
                <div class="form-group">
                    <label for="photo" class="form-label">صورة الغلاف</label>
                    <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
                </div>

                {{-- Description --}}
                <div class="form-group" style="grid-column: span 2;">
                    <label for="description" class="form-label">الوصف <span style="color: red;">*</span></label>
                    <textarea id="description" name="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                </div>

                {{-- Warnings & Advice --}}
                <div class="form-group">
                    <label for="warning" class="form-label">تحذيرات (اختياري)</label>
                    <textarea id="warning" name="warning" class="form-control" rows="2">{{ old('warning') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="advice" class="form-label">نصائح إضافية (اختياري)</label>
                    <textarea id="advice" name="advice" class="form-control" rows="2">{{ old('advice') }}</textarea>
                </div>

                {{-- Public/Private Toggle --}}
                @if($canCreatePublic)
                <div class="form-group" style="grid-column: span 2; background: var(--bg-gray); padding: 15px; border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" id="is_public" name="is_public" value="1" 
                               {{ old('is_public') ? 'checked' : '' }}
                               onchange="toggleClientSelection(this)"
                               style="width: 20px; height: 20px; cursor: pointer;">
                        <label for="is_public" style="margin: 0; font-weight: bold; color: var(--olive-dark);">جعل هذه الحمية عامة</label>
                    </div>
                    <small style="color: var(--text-medium); margin-right: 30px; display: block;">الحميات العامة تكون متاحة لجميع المستخدمين ولا ترتبط بعميل محدد.</small>
                </div>
                @else
                <input type="hidden" name="is_public" value="0">
                <div class="form-group" style="grid-column: span 2;">
                    <div class="alert" style="background-color: var(--olive-light); color: var(--olive-dark); border: 1px solid var(--olive-medium);">
                        📌 ملاحظة: ستكون هذه الحمية <strong>خاصة</strong> (يجب تحديد العملاء المستهدفين أدناه).
                    </div>
                </div>
                @endif

                {{-- Client Selection --}}
                <div id="clients-selection" style="grid-column: span 2; display: {{ old('is_public') ? 'none' : 'block' }};">
                    <h3 style="font-size: 1.1rem; color: var(--olive-dark); margin-bottom: 10px; border-bottom: 2px solid var(--olive-light); padding-bottom: 5px;">
                        👥 تحديد العملاء (جلسات استشارية مفتوحة) <span style="color: red;">*</span>
                    </h3>
                    
                    @if($clients->isEmpty())
                         <div style="padding: 20px; text-align: center; background: #fffbe6; border: 1px solid #ffe58f; border-radius: 8px;">
                            ⚠️ لا يوجد لديك عملاء لديهم جلسات استشارية "مفتوحة" حالياً.
                         </div>
                    @else
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; max-height: 300px; overflow-y: auto; padding: 5px;">
                            @foreach($clients as $client)
                            <label class="card" style="display: flex; align-items: center; gap: 15px; padding: 15px; cursor: pointer; transition: all 0.2s; border: 1px solid var(--border-color); box-shadow: none;">
                                <input type="checkbox" name="clients[]" value="{{ $client->clients_id }}" 
                                       id="client_{{ $client->clients_id }}"
                                       style="width: 18px; height: 18px;"
                                       onclick="this.parentElement.style.borderColor = this.checked ? 'var(--olive-medium)' : 'var(--border-color)'; this.parentElement.style.backgroundColor = this.checked ? 'var(--olive-light)' : 'white';">
                                <div>
                                    <div style="font-weight: bold; color: var(--text-dark);">{{ $client->user->Fname }} {{ $client->user->Lname }}</div>
                                    <div style="font-size: 0.85rem; color: var(--text-medium);">{{ $client->user->email }}</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    @endif
                    @error('clients') <div class="error-message" style="color: var(--red-accent); margin-top: 5px;">{{ $message }}</div> @enderror
                </div>

                {{-- Meals Selection --}}
                <div style="grid-column: span 2; margin-top: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 2px solid var(--olive-light); padding-bottom: 5px;">
                        <h3 style="font-size: 1.1rem; color: var(--olive-dark); margin: 0;">🍽️ اختيار الوجبات</h3>
                        <button type="button" onclick="suggestMealsAI()" class="btn-primary" 
                                style="background: rgba(132, 204, 22, 0.15); 
                                       color: #4d7c0f; 
                                       border: 2px solid #84cc16; 
                                       padding: 6px 16px; 
                        <button type="button" onclick="suggestMealsAI()" class="btn-primary" style="padding: 5px 15px; background: rgba(107, 142, 35, 0.15); color: #556B2F; border: 2px solid #6B8E23; font-size: 0.9rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(107, 142, 35, 0.2); transition: all 0.3s ease;">
                            <i class="fas fa-magic"></i> AI Suggest
                        </button>
                    </div>

                     <!-- Loading Indicator -->
                    <div id="ai-loading" style="display: none; text-align: center; padding: 15px; background: var(--blue-light); border-radius: 8px; margin-bottom: 15px; color: var(--blue-dark);">
                        <i class="fas fa-spinner fa-spin"></i> جاري تحليل القيود واقتراح الوجبات...
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; max-height: 400px; overflow-y: auto; padding: 5px;">
                        @foreach($meals as $meal)
                            <label class="card" style="display: flex; align-items: start; gap: 10px; padding: 10px; cursor: pointer; border: 1px solid var(--border-color); box-shadow: none;">
                                <input type="checkbox" name="meals[]" value="{{ $meal->meals_id }}" id="meal_{{ $meal->meals_id }}"
                                       style="width: 16px; height: 16px; margin-top: 5px;"
                                       onclick="this.parentElement.style.borderColor = this.checked ? 'var(--olive-medium)' : 'var(--border-color)'; this.parentElement.style.backgroundColor = this.checked ? 'var(--olive-light)' : 'white';">
                                <div>
                                    <strong style="color: var(--text-dark);">{{ $meal->name }}</strong>
                                    <div style="font-size: 0.8rem; color: var(--text-light); margin-top: 2px;">
                                        {{ $meal->calories }} kcal | {{ $meal->category->category_name ?? 'عام' }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Restrictions --}}
                <div style="grid-column: span 2; margin-top: 20px;">
                    <h3 style="font-size: 1.1rem; color: var(--olive-dark); margin-bottom: 10px; border-bottom: 2px solid var(--olive-light); padding-bottom: 5px;">
                        🚫 القيود الغذائية (اختياري)
                    </h3>
                    <div id="restrictions-container">
                        <p style="color: var(--text-light); font-style: italic; margin-bottom: 10px;">لا توجد قيود مضافة حالياً.</p>
                    </div>
                    <button type="button" class="btn-secondary" onclick="addRestriction()" style="margin-top: 10px;">
                        <i class="fas fa-plus"></i> إضافة قيد جديد
                    </button>
                </div>

            </div>

            <div style="margin-top: 30px; display: flex; gap: 15px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 10px 40px;">💾 حفظ الحمية</button>
                <a href="{{ route('shared.diets') }}" class="btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<script>
    let restrictionCount = 0;

    function toggleClientSelection(publicCheckbox) {
        const clientsSection = document.getElementById('clients-selection');
        clientsSection.style.display = publicCheckbox.checked ? 'none' : 'block';
    }

    function addRestriction() {
        // Clear "no restrictions" text if exists
        const container = document.getElementById('restrictions-container');
        if(container.querySelector('p')) container.innerHTML = '';

        const index = restrictionCount++;
        const html = `
            <div class="restriction-item" style="display: grid; grid-template-columns: 2fr 1.5fr 1fr 1.5fr auto; gap: 10px; align-items: end; background: var(--bg-gray); padding: 10px; border-radius: var(--radius-md); margin-bottom: 10px;">
             
                <div>
                    <label style="font-size: 0.8rem;">الحقل <span style="color: red;">*</span></label>
                    <select name="restrictions[${index}][field_name]" class="form-control" required>
                        <option value="" disabled selected>اختر الحقل</option>
                        <option value="calories">سعرات</option>
                        <option value="protein_g">بروتين</option>
                        <option value="carbs_g">كربوهيدرات</option>
                        <option value="fat_g">دهون</option>
                    </select>
                </div>
                <div>
                     <label style="font-size: 0.8rem;">العملية</label>
                     <select name="restrictions[${index}][operator]" class="form-control">
                        <option value="<"><</option>
                        <option value="<="><=</option>
                        <option value="=">=</option>
                        <option value=">=">>=</option>
                        <option value=">">></option>
                    </select>
                </div>
                 <div>
                    <label style="font-size: 0.8rem;">القيمة</label>
                    <input type="number" name="restrictions[${index}][value]" class="form-control" placeholder="0">
                </div>

                   <div>
                    <label style="font-size: 0.8rem;">الوصف (اختياري)</label>
                    <input type="text" name="restrictions[${index}][restriction]" class="form-control" placeholder="وصف القيد">
                </div>
                
                <button type="button" class="btn-danger" style="padding: 8px 12px;" onclick="this.parentElement.remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    // AI Suggest Logic (Existing, just slightly styled interaction)
     async function suggestMealsAI() {
        const dietName = document.getElementById('name').value;
        const description = document.getElementById('description').value;

        if (!dietName) { alert('يرجى إدخال اسم الحمية أولاً'); return; }

        // Collect restrictions
        const restrictions = [];
        document.querySelectorAll('.restriction-item').forEach(item => {
            const field = item.querySelector('[name*="[field_name]"]').value;
            const op = item.querySelector('[name*="[operator]"]').value;
            const val = item.querySelector('[name*="[value]"]').value;
            if(field && op && val) {
                restrictions.push({ field_name: field, operator: op, value: val, restriction: 'AI Filter' });
            }
        });

        if (restrictions.length === 0) { alert('يرجى إضافة قيد واحد على الأقل أولاً (مثل الحد الأقصى للسعرات) ليتمكن الذكاء الاصطناعي من العمل.'); return; }

        document.getElementById('ai-loading').style.display = 'block';

        try {
             const response = await fetch('{{ route("shared.diets.ai_suggest_meals") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ name: dietName, description: description, restrictions: restrictions })
            });
            const result = await response.json();

            if (result.success) {
                // Clear
                document.querySelectorAll('input[name="meals[]"]').forEach(cb => {
                    cb.checked = false; 
                    cb.parentElement.style.borderColor = 'var(--border-color)';
                    cb.parentElement.style.backgroundColor = 'white';
                });
                // Select
                let count = 0;
                result.data.meals.forEach(m => {
                    const cb = document.getElementById('meal_' + m.meals_id);
                    if(cb) {
                        cb.checked = true;
                        cb.parentElement.style.borderColor = 'var(--olive-medium)';
                        cb.parentElement.style.backgroundColor = 'var(--olive-light)';
                        cb.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        count++;
                    }
                });
                alert(`✨ تم اقتراح ${count} وجبة بنجاح!`);
            } else {
                alert('عذراً، لم نتمكن من العثور على اقتراحات: ' + result.message);
            }
        } catch (e) {
            alert('حدث خطأ في الاتصال.');
            console.error(e);
        } finally {
            document.getElementById('ai-loading').style.display = 'none';
        }
    }
</script>
@endsection