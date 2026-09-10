@extends('layouts.admin_app')

@section('title', 'إضافة مطعم جديد')

@push('styles')
<style>
    :root {
        --olive-dark: #556B2F;
        --olive-medium: #6B8E23;
        --olive-light: #F0F4E3; /* Approximate */
        --olive-very-light: #F9FAF5;
        --red-accent: #EF4444;
        --border-color: #E5E7EB;
        --text-dark: #1F2937;
    }

    .form-container {
        max-width: 1000px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f3f4f6;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--olive-dark);
        font-size: 15px;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group textarea,
    .form-group select,
    .form-group input[type="file"] {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        border-color: var(--olive-medium);
        outline: none;
        box-shadow: 0 0 0 3px rgba(107, 142, 35, 0.1);
    }

    /* Dynamic Section Styles */
    .dynamic-section {
        background-color: var(--olive-very-light);
        border: 1px solid #e5e7eb;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .section-title {
        font-size: 16px;
        font-weight: bold;
        color: var(--olive-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add-item {
        background: var(--olive-medium);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-add-item:hover {
        background: var(--olive-dark);
    }

    .dynamic-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .dynamic-item {
        display: flex;
        gap: 10px;
        align-items: center;
        background: white;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .btn-remove {
        color: var(--red-accent);
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        font-size: 16px;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .btn-remove:hover {
        background: #fee2e2;
    }

    .form-actions {
        margin-top: 40px;
        display: flex;
        gap: 15px;
    }

    .btn-submit {
        flex: 1;
        background: linear-gradient(135deg, var(--olive-medium) 0%, var(--olive-dark) 100%);
        color: white;
        padding: 14px;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(85, 107, 47, 0.3);
    }

    .btn-back {
        padding: 14px 25px;
        background: white;
        border: 1px solid var(--border-color);
        color: #4b5563;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-back:hover {
        background: #f9fafb;
        color: #111827;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <div>
            <h1 style="font-size: 24px; font-weight: bold; color: #111827;">إضافة مطعم جديد</h1>
            <p style="color: #6b7280; margin-top: 5px;">إنشاء ملف جديد للمطعم مع المواقع ومعلومات الاتصال</p>
        </div>
        <div>
            <img src="{{ asset('images/mealmate.png') }}" alt="MealMate" style="height: 40px; width: auto;">
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.restaurants.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="name">اسم المطعم *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="مثال: المطبخ الصحي" required>
        </div>

        <div class="form-group">
            <label for="description">وصف المطعم</label>
            <textarea id="description" name="description" rows="3" placeholder="وصف مختصر للمطعم...">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@restaurant.com">
        </div>

        <div class="form-group">
            <label for="state">حالة المطعم</label>
            <select id="state" name="state">
                <option value="active" {{ old('state') == 'active' ? 'selected' : '' }}>مفتوح (Open)</option>
                <option value="inactive" {{ old('state') == 'inactive' ? 'selected' : '' }}>مغلق (Closed)</option>
                <option value="pending" {{ old('state') == 'pending' ? 'selected' : '' }}>قيد الانتظار (Pending)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="manager_id">مدير المطعم</label>
            <select id="manager_id" name="manager_id" class="select2-manager">
                <option value="">-- اختر مدير المطعم --</option>
                @foreach($managers as $manager)
                    <option value="{{ $manager->user_id }}" {{ old('manager_id') == $manager->user_id ? 'selected' : '' }}>
                        {{ $manager->Fname }} {{ $manager->Lname }} - {{ $manager->email ?? $manager->phone }}
                    </option>
                @endforeach
            </select>
            <p style="font-size: 13px; color: #6b7280; margin-top: 5px;">يجب أن يكون للمستخدم دور "Restaurant Manager"</p>
        </div>

        <div class="form-group">
            <label for="commission_rate">نسبة عمولة المنصة (%) *</label>
            <input type="number" step="0.01" min="0" max="100" id="commission_rate" name="commission_rate" value="{{ old('commission_rate', '10.00') }}" placeholder="مثال: 10.00" required>
            <p style="font-size: 13px; color: #6b7280; margin-top: 5px;">نسبة العمولة المقتطعة للمنصة تلقائياً من كل طلبية للمطعم.</p>
        </div>

       <div class="col-md-6 mb-3">
                <label for="photo" class="form-label">صورة المطعم</label>
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                @error('photo')<p style="font-size: 13px; color: #6b7280; margin-top: 5px;">الحجم الموصى به: 1200x600px | الحد الأقصى: 2 ميجابايت</p>@enderror
             </div>

        <!-- Phone Numbers Section -->
        <div class="dynamic-section">
            <div class="section-header">
                <div class="section-title">
                    <span>📞</span> أرقام الهاتف
                </div>
                <button type="button" class="btn-add-item" onclick="addPhone()">
                    <span>+</span> إضافة رقم
                </button>
            </div>
            <div id="phones-list" class="dynamic-list">
                <div class="dynamic-item">
                    <input type="text" name="phones[]" placeholder="e.g., +1 234 567 890" required>
                    <!-- First one might not be removable or can rely on JS validation -->
                </div>
            </div>
        </div>

        <!-- Location Map Section -->
        <div class="dynamic-section">
            <div class="section-header">
                <div class="section-title">
                    <span>📍</span> موقع المطعم
                </div>
            </div>
            
            <p style="font-size: 14px; color: #666; margin-bottom: 10px;">قم بالضغط على الخريطة لتحديد موقع المطعم بدقة.</p>
            
            <div style="position: relative;">
                <div id="map" style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid #ccc; z-index: 1;"></div>
                <button type="button" id="locate-me-btn" style="position: absolute; top: 10px; right: 10px; z-index: 1000; background: white; border: 2px solid #ccc; padding: 7px; border-radius: 4px; cursor: pointer; font-size: 18px;" title="حدد موقعي الحالي">
                    📍
                </button>
            </div>
            
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            
            <div class="form-group" style="margin-top: 15px;">
                <label for="location_description" style="font-size: 14px;">وصف العنوان (اختياري)</label>
                <input type="text" name="location_description" id="location_description" placeholder="مثال: الرياض، حي الملز، شارع الجامعة" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.restaurants.index') }}" class="btn-back">إلغاء</a>
            <button type="submit" class="btn-submit">حفظ المطعم</button>
        </div>
    </form>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
    /* تخصيص Select2 */
    .select2-container--default .select2-selection--single {
        height: 45px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 8px 12px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        color: var(--text-dark);
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--olive-medium);
        box-shadow: 0 0 0 3px rgba(107, 142, 35, 0.1);
    }
    
    .select2-dropdown {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .select2-search--dropdown .select2-search__field {
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 8px;
    }
    
    .select2-results__option--highlighted {
        background-color: var(--olive-light) !important;
        color: var(--olive-dark) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    // تفعيل Select2 للبحث في قائمة المديرين
    $(document).ready(function() {
        $('.select2-manager').select2({
            placeholder: '-- اختر مدير المطعم --',
            allowClear: true,
            dir: 'rtl',
            language: {
                noResults: function() {
                    return "لا توجد نتائج";
                },
                searching: function() {
                    return "جاري البحث...";
                }
            }
        });
    });
    
    function addPhone() {
        const container = document.getElementById('phones-list');
        const newItem = document.createElement('div');
        newItem.className = 'dynamic-item';
        newItem.innerHTML = `
            <input type="text" name="phones[]" placeholder="e.g., +1 234 567 890" required>
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()" title="Remove">✕</button>
        `;
        container.appendChild(newItem);
    }

    // Map Initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Default View: Sana'a
        var defaultLat = 15.3694;
        var defaultLng = 44.1910;
        
        var map = L.map('map').setView([defaultLat, defaultLng], 15); 

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker;

        function updateMarker(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker([lat, lng]).addTo(map)
                .bindPopup("تم تحديد الموقع").openPopup();
        }

        map.on('click', function(e) {
            updateMarker(e.latlng.lat, e.latlng.lng);
        });
        
        // Add Search Bar (Geocoder)
        L.Control.geocoder({
            defaultMarkGeocode: false
        })
        .on('markgeocode', function(e) {
            var lat_y = e.geocode.center.lat;
            var lng_x = e.geocode.center.lng;
            var bbox = e.geocode.bbox;
            
            map.fitBounds(bbox);
            updateMarker(lat_y, lng_x);
        })
        .addTo(map);


        // Locate Me Button
        document.getElementById('locate-me-btn').addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser");
                return;
            }

            this.innerHTML = '⌛'; // Loading state

            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                
                map.setView([lat, lng], 17);
                updateMarker(lat, lng);
                document.getElementById('locate-me-btn').innerHTML = '📍';
            }, function(error) {
                let msg = "تعذر تحديد موقعك.";
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        msg = "تم رفض إذن الوصول للموقع. يرجى تفعيل الموقع من إعدادات المتصفح (أيقونة القفل أو الموقع في شريط العنوان).";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        msg = "معلومات الموقع غير متوفرة. تأكد من تشغيل GPS.";
                        break;
                    case error.TIMEOUT:
                        msg = "استغرق تحديد الموقع وقتاً طويلاً.";
                        break;
                    default:
                        msg = "حدث خطأ غير معروف أثناء تحديد الموقع.";
                        break;
                }
                alert(msg);
                document.getElementById('locate-me-btn').innerHTML = '📍';
            });
        });
    });
</script>
@endpush
@endsection
