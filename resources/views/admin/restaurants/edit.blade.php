@extends('layouts.admin_app')

@section('title', 'تعديل المطعم')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --olive-dark: #556B2F;
            --olive-medium: #6B8E23;
            --olive-light: #F0F4E3;
            --olive-very-light: #F9FAF5;
            --red-accent: #EF4444;
            --border-color: #E5E7EB;
            --text-dark: #1F2937;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            font-size: 22px;
            font-weight: bold;
            color: var(--text-dark);
            margin: 0;
        }

        .btn-back {
            background-color: var(--olive-light);
            color: var(--olive-dark);
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-back:hover {
            background-color: var(--border-color);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--olive-dark);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--olive-medium);
            outline: none;
            box-shadow: 0 0 0 3px rgba(107, 142, 35, 0.1);
        }

        .current-photo-container {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: var(--olive-very-light);
            border-radius: 8px;
            border: 1px dashed var(--olive-medium);
            margin-bottom: 10px;
        }

        .current-photo-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .photo-info {
            font-size: 13px;
            color: #666;
        }

        .btn-submit {
            background: var(--olive-dark);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #3A5A40;
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
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
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
            padding: 8px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .btn-remove {
            color: var(--red-accent);
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            font-size: 14px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-remove:hover {
            background: #fee2e2;
        }
    </style>
@endpush

@section('content')
    <div class="form-container">
        <div class="header-bar">
            <h1 class="page-title">تعديل المطعم</h1>
            <a href="{{ route('admin.restaurants.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> العودة للقائمة
            </a>
        </div>

        <form action="{{ route('admin.restaurants.update', $restaurant->restaurants_id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">اسم المطعم</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $restaurant->name) }}"
                    required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">وصف المطعم</label>
                <textarea name="description" id="description" class="form-control"
                    rows="3">{{ old('description', $restaurant->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" id="email" class="form-control"
                    value="{{ old('email', $restaurant->email) }}">
            </div>

            <div class="form-group">
                <label for="state" class="form-label">حالة المطعم</label>
                <select name="state" id="state" class="form-control">
                    <option value="active" {{ old('state', $restaurant->state) == 'active' ? 'selected' : '' }}>مفتوح (Open)
                    </option>
                    <option value="inactive" {{ old('state', $restaurant->state) == 'inactive' ? 'selected' : '' }}>مغلق
                        (Closed)</option>
                    <option value="pending" {{ old('state', $restaurant->state) == 'pending' ? 'selected' : '' }}>قيد الانتظار
                        (Pending)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="manager_id" class="form-label">مدير المطعم</label>
                <select name="manager_id" id="manager_id" class="form-control">
                    <option value="">-- اختر مدير المطعم --</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->user_id }}" {{ old('manager_id', $restaurant->manager_id) == $manager->user_id ? 'selected' : '' }}>
                            {{ $manager->full_name }} ({{ $manager->email ?? $manager->phone }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="commission_rate" class="form-label">نسبة عمولة المنصة (%) *</label>
                <input type="number" step="0.01" min="0" max="100" name="commission_rate" id="commission_rate" class="form-control"
                    value="{{ old('commission_rate', $restaurant->commission_rate ?? '10.00') }}" required>
                <small class="text-muted">نسبة العمولة المقتطعة للمنصة تلقائياً من كل طلبية للمطعم.</small>
            </div>

            <div class="form-group">
                <label for="photo" class="form-label">صورة المطعم (اتركها فارغة للاحتفاظ بالحالية)</label>
                @if($restaurant->photo_url)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $restaurant->photo_url) }}" alt="{{ $restaurant->name }}" width="100"
                            class="img-thumbnail">
                    </div>
                @endif
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                @error('photo')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Phone Numbers Section -->
            <div class="dynamic-section">
                <div class="section-header">
                    <div class="section-title">
                        <span>📞</span> أرقام الهاتف
                    </div>
                    <!-- Add functionality to backend required to fully utilize this in update -->
                    <button type="button" class="btn-add-item" onclick="addPhone()">
                        <span>+</span> إضافة رقم
                    </button>
                </div>
                <div id="phones-list" class="dynamic-list">
                    @if($restaurant->phones && $restaurant->phones->count() > 0)
                        @foreach($restaurant->phones as $phone)
                            <div class="dynamic-item">
                                <input type="text" name="phones[]" value="{{ $phone->phone_number }}" class="form-control" required
                                    style="border:none; padding:5px;">
                                <button type="button" class="btn-remove" onclick="this.parentElement.remove()"
                                    title="Remove">✕</button>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback if no phones -->
                        <div class="dynamic-item">
                            <input type="text" name="phones[]" placeholder="e.g., +1 234 567 890" class="form-control"
                                style="border:none;">
                            <button type="button" class="btn-remove" onclick="this.parentElement.remove()"
                                title="Remove">✕</button>
                        </div>
                    @endif
                </div>
                <p style="font-size:12px; color:#666; margin-top:5px;">ملاحظة: إزالة العناصر هنا سيؤدي لحذفها عند الحفظ.</p>
            </div>

            <!-- Location Map Section -->
            <div class="dynamic-section">
                <div class="section-header">
                    <div class="section-title">
                        <span>📍</span> موقع المطعم
                    </div>
                </div>

                <p style="font-size: 14px; color: #666; margin-bottom: 10px;">قم بالضغط على الخريطة لتحديث موقع المطعم.</p>

                <div style="position: relative;">
                    <div id="map"
                        style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid #ccc; z-index: 1;"></div>
                    <button type="button" id="locate-me-btn"
                        style="position: absolute; top: 10px; right: 10px; z-index: 1000; background: white; border: 2px solid #ccc; padding: 7px; border-radius: 4px; cursor: pointer; font-size: 18px;"
                        title="حدد موقعي الحالي">
                        📍
                    </button>
                </div>

                @php
                    $currentLocation = $restaurant->location;
                    $lat = $currentLocation ? $currentLocation->latitude_x : null;
                    $lng = $currentLocation ? $currentLocation->longitude_y : null;
                    $desc = $currentLocation ? $currentLocation->description : '';
                @endphp

                <input type="hidden" name="latitude" id="latitude" value="{{ $lat }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ $lng }}">

                <div class="form-group" style="margin-top: 15px;">
                    <label for="location_description" style="font-size: 14px;">وصف العنوان (اختياري)</label>
                    <input type="text" name="location_description" id="location_description" value="{{ $desc }}"
                        placeholder="مثال: الرياض، حي الملز، شارع الجامعة"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
            </div>

            <button type="submit" class="btn-submit">تحديث ملف المطعم</button>
        </form>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
        <script>
            function addPhone() {
                const container = document.getElementById('phones-list');
                const newItem = document.createElement('div');
                newItem.className = 'dynamic-item';
                newItem.innerHTML = `
                    <input type="text" name="phones[]" placeholder="e.g., +1 234 567 890" class="form-control" required style="border:none; padding:5px;">
                    <button type="button" class="btn-remove" onclick="this.parentElement.remove()" title="Remove">✕</button>
                `;
                container.appendChild(newItem);
            }

            // Map Initialization
            document.addEventListener('DOMContentLoaded', function () {
                // Default View: Sana'a or Current Location
                var initialLat = {{ $lat ?? 15.3694 }};
                var initialLng = {{ $lng ?? 44.1910 }};
                var hasLocation = {{ $lat ? 'true' : 'false' }};

                var map = L.map('map').setView([initialLat, initialLng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                var marker;

                if (hasLocation) {
                    marker = L.marker([initialLat, initialLng]).addTo(map)
                        .bindPopup("الموقع الحالي").openPopup();
                }

                function updateMarker(lat, lng) {
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;

                    if (marker) {
                        map.removeLayer(marker);
                    }
                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup("تم تحديد الموقع الجديد").openPopup();
                }

                map.on('click', function (e) {
                    updateMarker(e.latlng.lat, e.latlng.lng);
                });

                // Add Search Bar (Geocoder)
                L.Control.geocoder({
                    defaultMarkGeocode: false
                })
                    .on('markgeocode', function (e) {
                        var lat_y = e.geocode.center.lat;
                        var lng_x = e.geocode.center.lng;
                        var bbox = e.geocode.bbox;

                        map.fitBounds(bbox);
                        updateMarker(lat_y, lng_x);
                    })
                    .addTo(map);

                // Locate Me Button
                document.getElementById('locate-me-btn').addEventListener('click', function () {
                    if (!navigator.geolocation) {
                        alert("Geolocation is not supported by your browser");
                        return;
                    }

                    this.innerHTML = '⌛'; // Loading state

                    navigator.geolocation.getCurrentPosition(function (position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;

                        map.setView([lat, lng], 17);
                        updateMarker(lat, lng);
                        document.getElementById('locate-me-btn').innerHTML = '📍';
                    }, function (error) {
                        let msg = "تعذر تحديد موقعك.";
                        switch (error.code) {
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