@extends('layouts.admin_app')

@section('title', 'الموافقة على الوجبات المعلقة')

@push('styles')
<style>
    /* Using Unified Design System Variables */
    .pending-meals-container { padding: var(--spacing-lg); }
    .page-header { margin-bottom: var(--spacing-2xl); }
    
    .meals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: var(--spacing-xl);
    }
    
    .meal-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: transform var(--transition-fast);
        border: 1px solid var(--border-color);
        cursor: pointer;
    }
    .meal-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .meal-image {
        height: 200px;
        width: 100%;
        object-fit: cover;
        background-color: var(--bg-gray);
    }
    
    .meal-content { padding: var(--spacing-lg); }
    
    .meal-category {
        color: var(--blue-primary);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: var(--spacing-xs);
    }
    
    .meal-name {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: var(--spacing-sm);
        color: var(--text-dark);
    }
    
    .meal-price {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--green-success);
        margin-bottom: var(--spacing-sm);
    }
    
    .meal-description {
        color: var(--text-medium);
        font-size: 0.9rem;
        margin-bottom: var(--spacing-md);
        line-height: 1.5;
    }
    
    .ingredients-list {
        margin-bottom: var(--spacing-md);
        font-size: 0.9rem;
        color: var(--text-medium);
    }
    .ingredients-list strong {
        display: block;
        margin-bottom: var(--spacing-xs);
        color: var(--text-dark);
    }
    
    .meal-actions {
        display: flex;
        gap: var(--spacing-sm);
        margin-top: var(--spacing-md);
        padding-top: var(--spacing-md);
        border-top: 1px solid var(--border-color);
    }
    
    /* User Preferred Button Style - Modern & Effective */
    .btn-action {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        cursor: pointer;
        transition: background var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        text-decoration: none;
        font-size: 14px;
    }
    
    .btn-view { background: var(--olive-very-light); color: var(--text-dark); }
    .btn-view:hover { background: var(--olive-light); }
    
    .btn-approve { background: var(--olive-light); color: var(--olive-dark); }
    .btn-approve:hover { background: var(--olive-medium); color: white; }
    
    .btn-reject { background: var(--red-light); color: var(--red-accent); }
    .btn-reject:hover { background: #fee2e2; color: var(--red-dark); }

    .no-data {
        grid-column: 1 / -1;
        text-align: center;
        padding: 50px;
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        color: var(--text-medium);
    }

    /* Modal Styles - Using Global Variables */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        overflow-y: auto;
    }
    .modal-content {
        background-color: var(--bg-white);
        margin: 5% auto;
        padding: var(--spacing-xl);
        border-radius: var(--radius-lg);
        width: 90%;
        max-width: 800px;
        box-shadow: var(--shadow-xl);
        position: relative;
    }
    .close-modal {
        position: absolute;
        top: 20px;
        right: 25px;
        color: var(--text-light);
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10;
    }
    .close-modal:hover { color: var(--text-dark); }
    
    .modal-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
        margin-top: var(--spacing-lg);
    }
    .form-group { margin-bottom: var(--spacing-md); }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
        color: var(--text-medium);
    }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 14px;
        background: var(--bg-white);
        color: var(--text-dark);
    }
    .full-width { grid-column: 1 / -1; }
    
    .modal-actions {
        margin-top: var(--spacing-lg);
        display: flex;
        justify-content: flex-end;
        gap: var(--spacing-sm);
        border-top: 1px solid var(--border-color);
        padding-top: var(--spacing-lg);
    }
    
    /* Matching System Primary Color (Olive) */
    .btn-save {
        background: linear-gradient(135deg, var(--olive-medium) 0%, var(--olive-dark) 100%);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-weight: bold;
    }
    .btn-save:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
    
    /* Additional Action Buttons */
    .btn-ai-suggest {
        background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-dark) 100%);
        color: white;
        padding: 15px 30px;
        border: none;
        border-radius: var(--radius-lg);
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: var(--shadow-md);
        transition: transform var(--transition-fast);
    }
    .btn-ai-suggest:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }

    .btn-manual {
        background: var(--bg-white);
        color: var(--blue-primary);
        padding: 15px 30px;
        border: 2px solid var(--blue-primary);
        border-radius: var(--radius-lg);
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: all var(--transition-fast);
        text-decoration: none;
    }
    .btn-manual:hover { background: var(--bg-gray); }

    .btn-skip {
        background: var(--text-light); /* Grayish */
        color: white;
        padding: 15px 30px;
        border: none;
        border-radius: var(--radius-lg);
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: background var(--transition-fast);
    }
    .btn-skip:hover { background: var(--text-medium); }

    /* AI Reason Box */
    .ai-reason-box {
        background: linear-gradient(135deg, var(--blue-light-bg, #f0f4ff) 0%, white 100%);
        padding: 20px;
        border-radius: var(--radius-lg);
        margin-bottom: 25px;
        display: none;
        border-left: 4px solid var(--blue-primary);
    }
    .ai-reason-title {
        color: var(--blue-primary);
        font-size: 16px;
        display: block;
        margin-bottom: 8px;
    }
    .ai-reason-text {
        margin: 0;
        color: var(--text-medium);
        line-height: 1.6;
    }

    .loading-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 5;
        border-radius: var(--radius-lg);
    }
    .btn-cancel {
        background: var(--text-light);
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: var(--radius-md);
        font-weight: bold;
        cursor: pointer;
        font-size: 16px;
        transition: background var(--transition-fast);
    }
    .btn-cancel:hover { background: var(--text-medium); }

    .meal-image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        background-color: var(--bg-gray);
        height: 200px;
        width: 100%;
        color: var(--text-light); /* Icon color */
    }

    .restaurant-name {
        font-size: 0.9rem;
        color: var(--text-medium);
        margin-bottom: 10px;
    }

    .modal-icon-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .modal-icon { font-size: 3rem; margin-bottom: 10px; }
    .modal-title-custom { color: var(--text-dark); }
</style>
@endpush

@section('content')
<div class="pending-meals-container">
    <div class="page-header">
        <h1>الوجبات المضافة حديثاً (بانتظار الموافقة)</h1>
        <p>     </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="meals-grid">
        @forelse($pendingMeals as $meal)
            <div class="meal-card" onclick="openMealModal({{ $meal->meals_id }})">
                @if($meal->photo_url)
                    <img src="{{ asset('storage/' . $meal->photo_url) }}" alt="{{ $meal->name }}" class="meal-image">
                @else
                    <div class="meal-image-placeholder">🍽️</div>
                @endif
                
                <div class="meal-content">
                    <div class="meal-category">{{ $meal->category->category_name ?? 'غير مصنف' }}</div>
                    <div class="meal-name">{{ $meal->name }}</div>
                    <div class="meal-price">${{ number_format($meal->price, 2) }}</div>
                    
                    @if($meal->restaurant)
                        <div class="restaurant-name">
                            <i class="fas fa-store"></i> {{ $meal->restaurant->name }}
                        </div>
                    @endif

                    <div class="meal-actions">
                        <button type="button" class="btn-action btn-view">
                            <i class="fas fa-eye"></i> مراجعة
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-data">
                <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 15px; color: #28a745;"></i>
                <h3>لا توجد وجبات جديدة!</h3>
                <p>لا توجد وجبات معلقة للمراجعة في الوقت الحالي.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Meal Detail Modal -->
<div id="mealModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <div id="modalLoading" class="loading-overlay" style="display: none;">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
        </div>
        
        <h2 id="modalTitle" style="margin-bottom: 20px; color: #333;">مراجعة الوجبة</h2>
        
        <form id="mealForm" onsubmit="updateMeal(event)">
            <input type="hidden" id="meal_id" name="meal_id">
            
            <div class="modal-grid">
                <div class="form-group">
                    <label>الاسم</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label>السعر ($)</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>وقت التحضير (دقيقة)</label>
                    <input type="number" id="preparation_time" name="preparation_time">
                </div>
                
                <div class="form-group full-width">
                    <label>الوصف</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>

                <div class="full-width">
                    <h3 style="margin: 15px 0 10px; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">المعلومات الغذائية (للوجبة)</h3>
                </div>

                <div class="form-group">
                    <label>الكمية (جم)</label>
                    <input type="number" id="quantity_g" name="quantity_g" step="0.01">
                </div>
                <div class="form-group">
                    <label>السعرات</label>
                    <input type="number" id="calories" name="calories" step="0.01">
                </div>
                <div class="form-group">
                    <label>بروتين (جم)</label>
                    <input type="number" id="protein_g" name="protein_g" step="0.01">
                </div>
                <div class="form-group">
                    <label>دهون (جم)</label>
                    <input type="number" id="fat_g" name="fat_g" step="0.01">
                </div>
                <div class="form-group">
                    <label>كربوهيدرات (جم)</label>
                    <input type="number" id="carbs_g" name="carbs_g" step="0.01">
                </div>
                
                <div class="full-width">
                    <label>المكونات (للقراءة فقط)</label>
                    <div id="ingredientsList" style="background: #f9f9f9; padding: 10px; border-radius: 8px; font-size: 14px; color: #555;"></div>
                </div>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-save">حفظ التغييرات</button>
                <button type="button" class="btn-action btn-reject" onclick="rejectMeal()">رفض</button>
                <button type="button" class="btn-action btn-approve" onclick="approveMeal()">موافقة</button>
            </div>
        </form>
    </div>
</div>

<!-- Success Modal (بعد الموافقة) -->
<div id="successModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <span class="close-modal" onclick="closeSuccessModal()">&times;</span>
        
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 4rem; margin-bottom: 20px;">✅</div>
            <h2 style="color: var(--green-success); margin-bottom: 15px;">تمت الموافقة على الوجبة بنجاح!</h2>
            <p style="color: var(--text-medium); margin-bottom: 30px;">يمكنك الآن اختيار الحميات المناسبة لهذه الوجبة</p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <button type="button" class="btn-ai-suggest" onclick="askAI()">
                    <span style="font-size: 20px;">🤖</span>
                    سؤال الذكاء الاصطناعي
                </button>
                
                <button type="button" class="btn-manual" onclick="showDietModalManual()">
                    اختيار يدوي
                </button>
                
                <button type="button" class="btn-skip" onclick="skipDietSelection()">
                    تخطي
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Diet Selection Modal -->
<div id="dietModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeDietModal()">&times;</span>
        <div id="dietModalLoading" class="loading-overlay" style="display: none;">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
        </div>
        
        <div class="modal-icon-header">
            <div class="modal-icon">🔍</div>
            <h2 class="modal-title-custom">اختر الحميات المناسبة لهذه الوجبة</h2>
        </div>
        
        <div id="ai-reason" class="ai-reason-box">
            <div style="display: flex; align-items: start; gap: 12px;">
                <span style="font-size: 24px;">🤖</span>
                <div>
                    <strong class="ai-reason-title">✨ توصية الذكاء الاصطناعي:</strong>
                    <p id="ai-reason-text" class="ai-reason-text"></p>
                </div>
            </div>
        </div>
        
        <div id="diets-list" style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
            <!-- Diets will be populated here -->
        </div>
        
        <div class="modal-actions">
            <button type="button" class="btn-save" onclick="confirmApproval()">
                <i class="fas fa-check"></i> تأكيد والموافقة على الوجبة
            </button>
            <button type="button" class="btn-action btn-cancel" onclick="closeDietModal()">
                إلغاء
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const modal = document.getElementById('mealModal');
    const loading = document.getElementById('modalLoading');
    let currentMealId = null;

    function openMealModal(id) {
        currentMealId = id;
        modal.style.display = 'block';
        loading.style.display = 'flex';
        
        // Fetch meal details
        fetch(`/specialist/meals/${id}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const meal = data.meal;
                    document.getElementById('meal_id').value = meal.meals_id;
                    document.getElementById('name').value = meal.name;
                    document.getElementById('price').value = meal.price;
                    document.getElementById('description').value = meal.description;

                    document.getElementById('preparation_time').value = meal.preparation_time;
                    document.getElementById('quantity_g').value = meal.quantity_g;
                    document.getElementById('calories').value = meal.calories;
                    document.getElementById('protein_g').value = meal.protein_g;
                    document.getElementById('fat_g').value = meal.fat_g;
                    document.getElementById('carbs_g').value = meal.carbs_g;
                    
                    // Ingredients
                    const ingredientsHtml = meal.ingredients.map(ing => 
                        `<div>• ${ing.name_ar} (${ing.quantity_g}g)</div>`
                    ).join('') || 'No ingredients listed';
                    document.getElementById('ingredientsList').innerHTML = ingredientsHtml;
                }
                loading.style.display = 'none';
            })
            .catch(err => {
                console.error(err);
                alert('خطأ في تحميل تفاصيل الوجبة');
                closeModal();
            });
    }

    function closeModal() {
        modal.style.display = 'none';
    }
    
    window.onclick = function(event) {
        if (event.target == modal) closeModal();
    }

    function updateMeal(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('mealForm'));
        const data = Object.fromEntries(formData.entries());
        
        // Convert to PUT request
        fetch(`/specialist/meals/${currentMealId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('تم حفظ التغييرات بنجاح. يمكنك الآن الموافقة على الوجبة.');
            } else {
                alert('خطأ في حفظ التغييرات');
            }
        })
        .catch(err => console.error(err));
    }

    async function approveMeal() {
        // إغلاق modal الوجبة
        closeModal();
        
        // عرض نافذة النجاح
        document.getElementById('successModal').style.display = 'block';
    }
    
    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
    }
    
    async function askAI() {
        // إغلاق نافذة النجاح
        closeSuccessModal();
        
        // عرض loading في modal الحميات
        const dietModal = document.getElementById('dietModal');
        const dietLoading = document.getElementById('dietModalLoading');
        
        dietModal.style.display = 'block';
        dietLoading.style.display = 'flex';
        
        try {
            // استدعاء AI لاقتراح الحميات
            const response = await fetch(`/specialist/meals/${currentMealId}/ai-suggest-diets`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                // عرض الحميات المقترحة
                showDietModalWithData(result.data);
            } else {
                alert('Error: ' + result.message);
                closeDietModal();
            }
        } catch (error) {
            console.error(error);
            alert('خطأ في الاتصال بخدمة الذكاء الاصطناعي');
            closeDietModal();
        } finally {
            dietLoading.style.display = 'none';
        }
    }
    
    async function showDietModalManual() {
        // إغلاق نافذة النجاح
        closeSuccessModal();
        
        // عرض loading
        const dietModal = document.getElementById('dietModal');
        const dietLoading = document.getElementById('dietModalLoading');
        
        dietModal.style.display = 'block';
        dietLoading.style.display = 'flex';
        
        try {
            // جلب جميع الحميات بسرعة (بدون AI)
            const response = await fetch(`/specialist/meals/diets`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                // عرض جميع الحميات بدون تحديد مسبق
                result.data.suggested_diet_ids = []; // إزالة الاقتراحات
                document.getElementById('ai-reason').style.display = 'none'; // إخفاء سبب AI
                showDietModalWithData(result.data);
            }
        } catch (error) {
            console.error(error);
            alert('خطأ في جلب الحميات');
            closeDietModal();
        } finally {
            dietLoading.style.display = 'none';
        }
    }
    
    async function skipDietSelection() {
        if (!confirm('هل تريد الموافقة على الوجبة بدون إضافتها لأي حمية؟')) return;
        
        closeSuccessModal();
        
        try {
            const response = await fetch(`/specialist/meals/${currentMealId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ diet_ids: [] })
            });
            
            const result = await response.json();
            if (result.success) {
                alert('تمت الموافقة على الوجبة بنجاح');
                window.location.reload();
            }
        } catch (error) {
            console.error(error);
            alert('خطأ في الموافقة على الوجبة');
        }
    }

    function showDietModalWithData(data) {
        const dietModal = document.getElementById('dietModal');
        const dietsList = document.getElementById('diets-list');
        const reasonDiv = document.getElementById('ai-reason');
        const reasonText = document.getElementById('ai-reason-text');
        
        // عرض سبب الاقتراح
        if (data.reason && data.suggested_diet_ids && data.suggested_diet_ids.length > 0) {
            reasonDiv.style.display = 'block';
            reasonText.textContent = data.reason;
        } else {
            reasonDiv.style.display = 'none';
        }
        
        // بناء قائمة الحميات
        let html = '';
        if (data.all_diets && data.all_diets.length > 0) {
            data.all_diets.forEach(diet => {
                const isChecked = data.suggested_diet_ids.includes(diet.diets_id);
                html += `
                    <label style="display: flex; align-items: start; gap: 12px; padding: 15px; border: 2px solid ${isChecked ? 'var(--blue-primary)' : 'var(--border-color)'}; border-radius: 8px; margin-bottom: 10px; cursor: pointer; background: ${isChecked ? 'var(--blue-light-bg, #f0f4ff)' : 'white'}; transition: all 0.2s;" 
                           onmouseover="this.style.borderColor='var(--blue-primary)'" 
                           onmouseout="this.style.borderColor='${isChecked ? 'var(--blue-primary)' : 'var(--border-color)'}'">
                        <input type="checkbox" name="diet_ids[]" value="${diet.diets_id}" ${isChecked ? 'checked' : ''} 
                               style="margin-top: 3px; width: 18px; height: 18px; accent-color: var(--blue-primary); cursor: pointer;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 5px;">
                                ${diet.name}
                                ${isChecked ? '<span style="color: var(--blue-primary); font-size: 12px; margin-left: 8px;">✨ مقترح من AI</span>' : ''}
                            </div>
                            <div style="font-size: 13px; color: var(--text-medium);">${diet.description || 'No description available'}</div>
                        </div>
                    </label>
                `;
            });
        } else {
            html = '<p style="text-align: center; color: var(--text-light); padding: 40px;">لا توجد حميات عامة متاحة</p>';
        }
        
        dietsList.innerHTML = html;
        dietModal.style.display = 'block';
    }

    function closeDietModal() {
        document.getElementById('dietModal').style.display = 'none';
    }

    async function confirmApproval() {
        // جمع الحميات المختارة
        const selectedDiets = Array.from(document.querySelectorAll('input[name="diet_ids[]"]:checked'))
            .map(cb => parseInt(cb.value));
        
        if (selectedDiets.length === 0) {
            if (!confirm('لم يتم اختيار أي حمية. الموافقة على الوجبة دون إضافتها لأي حمية؟')) return;
        }
        
        // عرض loading
        document.getElementById('dietModalLoading').style.display = 'flex';
        
        // الموافقة على الوجبة مع الحميات
        try {
            const response = await fetch(`/specialist/meals/${currentMealId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    diet_ids: selectedDiets
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                window.location.reload();
            } else {
                alert('خطأ: ' + result.message);
                document.getElementById('dietModalLoading').style.display = 'none';
            }
        } catch (error) {
            console.error(error);
            alert('خطأ في الموافقة على الوجبة');
            document.getElementById('dietModalLoading').style.display = 'none';
        }
    }

    // Fallback approval without diets (in case AI fails)
    async function fallbackApprove() {
        try {
            const response = await fetch(`/specialist/meals/${currentMealId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ diet_ids: [] })
            });
            
            const result = await response.json();
            if (result.success) {
                alert('تمت الموافقة على الوجبة بنجاح (بدون اقتراحات حمية)');
                window.location.reload();
            }
        } catch (error) {
            console.error(error);
            alert('خطأ في الموافقة على الوجبة');
        }
    }

    function rejectMeal() {
        if(!confirm('هل أنت متأكد من رفض هذه الوجبة؟')) return;
        
        fetch(`/specialist/meals/${currentMealId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => window.location.reload());
    }
</script>
@endpush
@endsection
