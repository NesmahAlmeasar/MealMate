@extends('layouts.admin_app')

@section('title', $diet->name)

@push('styles')
    <style>
        .diet-details-container { display: flex; gap: 20px; flex-wrap: wrap; }
        .diet-details-info { flex: 2; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); min-width: 300px; }
        .diet-details-info h2 { font-size: 20px; color: var(--olive-dark); margin-bottom: 10px; border-bottom: 2px solid var(--olive-light); padding-bottom: 5px; }
        .diet-details-info p { font-size: 14px; line-height: 1.6; color: var(--text-dark); margin-bottom: 20px; }
        .diet-image-details { width: 100%; height: 250px; overflow: hidden; border-radius: 8px; margin-top: 15px; }
        .diet-image-details img { width: 100%; height: 100%; object-fit: cover; }
        
        .meals-section { flex: 3; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); min-width: 300px; }
        .meals-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; margin-top: 15px; }
        .meal-card { background-color: var(--olive-very-light); border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); transition: transform 0.2s; position: relative; cursor: pointer; }
        .meal-card:hover { transform: translateY(-3px); }
        .meal-image { width: 100%; height: 120px; overflow: hidden; background-color: #E5E7EB; }
        .meal-info { padding: 10px; }
        .meal-name { font-size: 14px; font-weight: bold; color: var(--olive-dark); margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .meal-restaurant { font-size: 11px; color: var(--text-light); height: 30px; overflow: hidden; text-overflow: ellipsis; }

        .diet-actions { margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px; display: flex; justify-content: center; gap: 15px; }
        .page-title { font-size: 24px; font-weight: bold; color: var(--text-dark); }
        .diets-header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); }

        /* Action Buttons Style */
        .action-buttons { display: flex; justify-content: center; gap: 10px; }
        .action-btn { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; font-size: 16px; }
        .action-btn.edit { background-color: #E0F2F1; color: #00897B; }
        .action-btn.edit:hover { background-color: #B2DFDB; }
        .action-btn.delete { background-color: #FFEBEE; color: #D32F2F; }
        .action-btn.delete:hover { background-color: #FFCDD2; }

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 25px; border-radius: 12px; width: 90%; max-width: 700px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; position: absolute; top: 15px; right: 20px; cursor: pointer; transition: color 0.2s; }
        .close-btn:hover { color: var(--olive-dark); }
        
        /* Checkbox List */
        .meal-checkbox-list { max-height: 400px; overflow-y: auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 10px; margin: 20px 0; padding: 5px; }
        .meal-checkbox-item { display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: background-color 0.2s; }
        .meal-checkbox-item:hover { background-color: var(--olive-very-light); }
        .meal-checkbox-item input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--olive-dark); cursor: pointer; }
        .meal-checkbox-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
        .meal-checkbox-info { display: flex; flex-direction: column; }
        .meal-checkbox-name { font-weight: bold; font-size: 14px; }
        .meal-checkbox-meta { font-size: 12px; color: #666; }

        .btn-add-meals { background-color: var(--olive-dark); color: white; padding: 10px 20px; border-radius: 6px; border: none; cursor: pointer; font-weight: bold; width: 100%; transition: background-color 0.2s; }
        .btn-add-meals:hover { background-color: #4a5e29; }
        
        .meals-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 2px solid var(--olive-light); padding-bottom: 10px; }
        .btn-open-modal { background-color: var(--olive-light); color: var(--olive-dark); border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 5px; }
        .btn-open-modal:hover { background-color: #d1e0b5; }

        /* Meal Detail Popup */
        .meal-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .nutrition-info { display: flex; gap: 15px; margin-top: 15px; background: #f9f9f9; padding: 10px; border-radius: 8px; }
        .nutrition-item { text-align: center; flex: 1; }
        .nutrition-value { font-weight: bold; color: var(--olive-dark); font-size: 16px; }
        .nutrition-label { font-size: 12px; color: #666; }

        @media (max-width: 768px) {
            .diet-details-container { flex-direction: column; }
            .meal-detail-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
 
    <div class="page-content">
        {{-- Header Bar --}}
        <div class="diets-header-bar">
            <div>
                <h1 class="page-title">{{ $diet->name }}</h1>
                <span class="badge {{ $diet->is_public ? 'bg-success' : 'bg-secondary' }}" style="padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; margin-right: 10px;">
                    {{ $diet->is_public ? 'عام' : 'خاص' }}
                </span>
            </div>
            
            @if((Auth::user()->hasRole('Nutrition Manager') || Auth::user()->hasRole('Admin')) || (Auth::user()->hasRole('Specialist') && Auth::id() == $diet->nutritionist_id))
            <div class="action-buttons">
                <a href="{{ route('shared.diets.edit', $diet->diets_id) }}" class="action-btn edit" title="تعديل">
                    ✏️
                </a>
                
                <form action="{{ route('shared.diets.destroy', $diet->diets_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الحمية؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn delete" title="حذف">
                        🗑️
                    </button>
                </form>
            </div>
            @endif
        </div>

        <div class="diet-details-container">
            {{-- Info Section --}}
            <div class="diet-details-info">
                <h2>الوصف</h2>
                <p>{{ $diet->description }}</p>

                @if($diet->warning)
                <div style="background-color: #FFF3CD; color: #856404; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px;">
                    <strong>⚠️ تحذير:</strong> {{ $diet->warning }}
                </div>
                @endif
                
                @if($diet->advice)
                <div style="background-color: #D1E7DD; color: #0F5132; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px;">
                    <strong>💡 نصيحة:</strong> {{ $diet->advice }}
                </div>
                @endif

                <div class="diet-image-details">
                    @if($diet->photo_url)
                        <img src="{{ Storage::url($diet->photo_url) }}" alt="{{ $diet->name }}">
                    @else
                        <img src="{{ asset('images/default-diet.jpg') }}" alt="Default Diet Image">
                    @endif
                </div>
            </div>

            {{-- Meals Section --}}
            <div class="meals-section">
                <div class="meals-header">
                    <h2 style="border:none; margin:0;">الوجبات المرتبطة ({{ $diet->meals->count() }})</h2>
                    @if((Auth::user()->hasRole('Nutrition Manager') || Auth::user()->hasRole('Admin')) || (Auth::user()->hasRole('Specialist') && Auth::id() == $diet->nutritionist_id))
                    <button class="btn-open-modal" id="btn-manage-meals">
                        <span>➕ إضافة / حذف وجبات</span>
                    </button>
                    @endif
                </div>

                @if($diet->meals->isEmpty())
                    <div style="text-align: center; color: #777; padding: 30px;">
                        <p>لا توجد وجبات مرتبطة بهذه الحمية حالياً.</p>
                    </div>
                @else
                    <div class="meals-grid">
                        @foreach($diet->meals as $meal)
                        <div class="meal-card" onclick='showMealDetails(@json($meal))'>
                            <div class="meal-image">
                                @if($meal->image_url)
                                    <img src="{{ Storage::url($meal->image_url) }}" alt="{{ $meal->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ccc;">لا توجد صورة</div>
                                @endif
                            </div>
                            <div class="meal-info">
                                <div class="meal-name" title="{{ $meal->name }}">{{ $meal->name }}</div>
                                <div class="meal-restaurant">{{ $meal->calories }} سعرة حرارية</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal: Manage Meals --}}
    @if(isset($allMeals))
    <div id="manage-meals-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('manage-meals-modal')">&times;</span>
            <h2>إدارة وجبات الحمية</h2>
            <p style="font-size: 13px; color: #666; margin-bottom: 15px;">قم بتحديد الوجبات التي تود ربطها بهذه الحمية. إلغاء التحديد سيؤدي لحذف الرابط.</p>
            
            <form action="{{ route('shared.diets.sync_meals', $diet->diets_id) }}" method="POST">
                @csrf
                <div class="meal-checkbox-list">
                    @foreach($allMeals as $meal)
                    <label class="meal-checkbox-item">
                        <input type="checkbox" name="meal_ids[]" value="{{ $meal->meals_id }}" 
                            {{ $diet->meals->contains('meals_id', $meal->meals_id) ? 'checked' : '' }}>
                        
                        @if($meal->image_url)
                            <img src="{{ Storage::url($meal->image_url) }}" alt="{{ $meal->name }}">
                        @else
                            <div style="width:50px; height:50px; background:#ddd; border-radius:6px;"></div>
                        @endif
                        
                        <div class="meal-checkbox-info">
                            <span class="meal-checkbox-name">{{ $meal->name }}</span>
                            <span class="meal-checkbox-meta">{{ $meal->calories }} cal | {{ $meal->category->name ?? 'بدون تصنيف' }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                <button type="submit" class="btn-add-meals">حفظ التغييرات</button>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal: Meal Details Popup --}}
    <div id="meal-details-modal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <span class="close-btn" onclick="closeModal('meal-details-modal')">&times;</span>
            <h2 id="popup-meal-name">اسم الوجبة</h2>
            
            <div class="meal-image" style="height: 200px; border-radius: 8px; margin-bottom: 15px;">
                <img id="popup-meal-image" src="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
            </div>

            <p id="popup-meal-desc" style="color: #666; margin-bottom: 15px;"></p>

            <div class="nutrition-info">
                <div class="nutrition-item">
                    <div class="nutrition-value" id="popup-cal">0</div>
                    <div class="nutrition-label">سعرات</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-value" id="popup-protein">0g</div>
                    <div class="nutrition-label">بروتين</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-value" id="popup-fat">0g</div>
                    <div class="nutrition-label">دهون</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-value" id="popup-carbs">0g</div>
                    <div class="nutrition-label">كارب</div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Manage Meals Modal
    const manageBtn = document.getElementById('btn-manage-meals');
    const manageModal = document.getElementById('manage-meals-modal');

    if(manageBtn) {
        manageBtn.addEventListener('click', () => {
             manageModal.style.display = 'block';
        });
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }

    // Show Meal Details Modal
    window.showMealDetails = function(meal) {
        document.getElementById('popup-meal-name').textContent = meal.name;
        document.getElementById('popup-meal-desc').textContent = meal.description || 'لا يوجد وصف متاح';
        document.getElementById('popup-cal').textContent = meal.calories;
        document.getElementById('popup-protein').textContent = meal.protein_g + 'g';
        document.getElementById('popup-fat').textContent = meal.fat_g + 'g';
        document.getElementById('popup-carbs').textContent = meal.carbs_g + 'g';
        
        const img = document.getElementById('popup-meal-image');
        if (meal.image_url) {
            img.src = '/storage/' + meal.image_url; 
        } else {
            img.src = ''; // Placeholder or hide
        }

        document.getElementById('meal-details-modal').style.display = 'block';
    }
</script>
@endpush