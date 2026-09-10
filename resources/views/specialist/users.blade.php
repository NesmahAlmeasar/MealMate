@extends('layouts.admin_app')

@section('title', 'جلسات الاستشارة')

@push('styles')
<style>
    :root {
        --spacing-xs: 0.25rem;
        --spacing-sm: 0.5rem;
        --spacing-md: 1rem;
        --spacing-lg: 1.5rem;
        --spacing-xl: 2rem;
        --radius-sm: 0.25rem;
        --radius-md: 0.5rem;
        --radius-lg: 1rem;
        --primary-color: #6B8E23;
        --bg-gray-dark: #f3f4f6;
        --text-dark: #1f2937;
        --text-light: #6b7280;
        --border-color: #e5e7eb;
    }
    
    * {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-sizing: border-box;
    }
    
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }
    
    .card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }
    
    /* تحسين التصميم للمودال */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.6);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        padding: var(--spacing-md);
        overflow-y: auto;
    }
    
    .modal-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 700px;
        max-height: 90vh;
        overflow: hidden;
        position: relative;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        margin: auto;
        animation: modalFadeIn 0.3s ease;
    }
    
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modal-body-content {
        max-height: calc(80vh - 200px);
        overflow-y: auto;
        padding-right: 5px;
    }
    
    .modal-body-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .modal-body-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .modal-body-content::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .modal-body-content::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* تحسين الجدول للشاشات الصغيرة */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: var(--spacing-md);
        }
        
        .card {
            padding: var(--spacing-sm);
            margin: 0 -10px;
            border-radius: var(--radius-md);
            width: calc(100% + 20px);
        }
        
        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            min-width: unset;
            width: 100%;
        }
        
        th, td {
            min-width: 120px;
            padding: var(--spacing-sm) !important;
        }
        
        td:first-child {
            /* min-width: 180px; Removed forced width */
        }
        
        /* تحسين عرض الأزرار على الشاشات الصغيرة */
        .action-buttons {
            flex-wrap: wrap;
            gap: 4px;
        }
        
        .action-buttons a {
            width: 28px;
            height: 28px;
            font-size: 0.8rem;
        }
    }
    
    @media (max-width: 576px) {
        :root {
            --spacing-xl: 1rem;
            --spacing-lg: 1rem;
        }
        
        .container-padding {
            padding: var(--spacing-md) !important;
        }
        
        .modal-content {
            width: 95%;
            max-height: 95vh;
        }
        
        .modal-body-grid {
            grid-template-columns: 1fr !important;
            gap: var(--spacing-md) !important;
        }
    }
    
    /* تحسين الأزرار */
    .btn-hover-effect {
        transition: all 0.3s ease;
    }
    
    .btn-hover-effect:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    /* تحسين العناوين */
    h1, h2, h3, h4 {
        font-weight: 700;
        margin-bottom: var(--spacing-md);
        line-height: 1.3;
    }
    
    /* تحسين الصور */
    img {
        max-width: 100%;
        height: auto;
        display: block;
    }
    
    /* تحسين الفقرات الفارغة */
    .empty-state {
        text-align: center;
        padding: var(--spacing-xl) !important;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: var(--spacing-md);
    }
    
    /* تحسين التبويبات في المودال */
    .data-card {
        padding: var(--spacing-lg);
        border-radius: 12px;
        margin-bottom: var(--spacing-lg);
    }
    
    .data-card h4 {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 var(--spacing-md) 0;
        font-size: 1.1rem;
    }
    
    .data-item {
        background: white;
        padding: 10px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    
    .data-item:last-child {
        margin-bottom: 0;
    }
    
    .data-label {
        color: #64748b;
        font-weight: 500;
    }
    
    .data-value {
        color: #1e293b;
        font-weight: 600;
    }
    
    /* تحسين علامات الأمراض والحساسية */
    .tag-container {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }
    
    .tag {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }
    
    /* رسائل التنبيه */
    .alert-message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .alert-success {
        background: #10b981;
        color: white;
    }
    
    .alert-error {
        background: #ef4444;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-padding" style="padding: var(--spacing-xl); min-height: calc(100vh - 200px);">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl); flex-wrap: wrap; gap: var(--spacing-md);">
        <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; color: var(--text-dark); margin: 0;">
            جلسات الاستشارة
        </h1>
    </div>

    <div class="card" style="overflow: hidden;">
        @if($consultations->isEmpty())
            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl);">
                <i class="fas fa-calendar-times"></i>
                <p style="color: var(--text-light); font-size: 1.1rem; margin: 0;">
                    لا توجد جلسات استشارة حالياً.
                </p>
            </div>
        @else
            <div style="width: 100%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); text-align: right; background: #f9fafb;">
                            <th style="padding: 12px; color: var(--text-light); font-weight: 600;">المستخدم</th>
                            <th style="padding: 12px; color: var(--text-light); font-weight: 600;">فترة الاستشارة</th>
                            <th style="padding: 12px; color: var(--text-light); font-weight: 600;">النوع</th>
                            <th style="padding: 12px; color: var(--text-light); font-weight: 600;">الحالة</th>
                            <th style="padding: 12px; color: var(--text-light); font-weight: 600;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consultations as $consultation)
                            @php
                                $user = $consultation->client;
                                $client = $user->client ?? null;
                                $hasData = $client && $client->bodyData && $client->lifestyle;
                                $isOpen = strtolower($consultation->status) == 'open' || strtolower($consultation->status) == 'active';
                            @endphp
                            <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s;" 
                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                
                                {{-- المستخدم --}}
                                <td style="padding: 12px;">
                                    <div style="display: flex; align-items: center; gap: var(--spacing-sm);">
                                        <img src="{{ $user->photo_url ? Storage::url($user->photo_url) : 'https://ui-avatars.com/api/?name='.urlencode($user->Fname.' '.$user->Lname).'&background=random' }}" 
                                             alt="{{ $user->Fname }}"
                                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; cursor: pointer; border: 2px solid transparent; transition: border-color 0.2s;"
                                             onclick="openUserModal('modal-{{ $consultation->consultation_id }}')"
                                             onmouseover="this.style.borderColor='var(--primary-color)'"
                                             onmouseout="this.style.borderColor='transparent'"
                                        >
                                        <div style="min-width: 0;">
                                            <div style="font-weight: 600; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $user->Fname }} {{ $user->Lname }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- فترة الاستشارة --}}
                                <td style="padding: 12px;">
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <i class="fas fa-play-circle" style="color: #10b981; font-size: 0.85rem; flex-shrink: 0;"></i>
                                            <span style="font-size: 0.85rem; white-space: nowrap;">{{ $consultation->start_time ? $consultation->start_time->format('Y-m-d H:i') : '-' }}</span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <i class="fas fa-stop-circle" style="color: #ef4444; font-size: 0.85rem; flex-shrink: 0;"></i>
                                            <span style="font-size: 0.85rem; white-space: nowrap;">{{ $consultation->end_time ? $consultation->end_time->format('Y-m-d H:i') : '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- النوع --}}
                                <td style="padding: 12px;">
                                    <span style="background: #e0f2fe; color: #0284c7; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 500; display: inline-block; white-space: nowrap;">
                                        {{ $consultation->type->name ?? 'DASH Diet' }}
                                    </span>
                                </td>
                                
                                {{-- الحالة --}}
                                <td style="padding: 12px;">
                                    @if($isOpen)
                                        <span style="background: #dcfce7; color: #16a34a; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 500; display: inline-block; white-space: nowrap;">
                                            مفتوحة
                                        </span>
                                    @else
                                        <span style="background: #f3f4f6; color: #6b7280; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 500; display: inline-block; white-space: nowrap;">
                                            {{ $consultation->status }}
                                        </span>
                                    @endif
                                </td>
                                
                                {{-- الإجراءات --}}
                                <td style="padding: 12px;">
                                    <div class="action-buttons" style="display: flex; gap: var(--spacing-xs);">
                                        @if($isOpen)
                                            {{-- زر المحادثة --}}
                                            <a href="{{ route('specialist.messages', ['user_id' => $user->user_id]) }}" 
                                               title="محادثة"
                                               style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: var(--bg-gray-dark); color: var(--text-dark); border-radius: var(--radius-md); transition: all 0.2s; text-decoration: none;"
                                               onmouseover="this.style.background='var(--primary-color)'; this.style.color='white';"
                                               onmouseout="this.style.background='var(--bg-gray-dark)'; this.style.color='var(--text-dark)';"
                                            >
                                                <i class="fas fa-comment-dots"></i>
                                            </a>

                                            {{-- زر إضافة إشعار --}}
                                            <button onclick="openNotificationModal('notify-modal-{{ $user->user_id }}')" 
                                                title="إرسال إشعار"
                                                style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: var(--bg-gray-dark); color: var(--text-dark); border:none; border-radius: var(--radius-md); transition: all 0.2s; cursor: pointer;"
                                                onmouseover="this.style.background='var(--primary-color)'; this.style.color='white';"
                                                onmouseout="this.style.background='var(--bg-gray-dark)'; this.style.color='var(--text-dark)';"
                                            >
                                                <i class="fas fa-bell"></i>
                                            </button>

                                            {{-- زر إضافة/تعديل الحمية --}}
                                            @if($consultation->diet_id)
                                                <a href="{{ route('specialist.diets.edit', $consultation->diet_id) }}" 
                                                   title="تعديل الحمية"
                                                   style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: var(--bg-gray-dark); color: #EAB308; border-radius: var(--radius-md); transition: all 0.2s; text-decoration: none;"
                                                   onmouseover="this.style.background='#CA8A04'; this.style.color='white';"
                                                   onmouseout="this.style.background='var(--bg-gray-dark)'; this.style.color='#EAB308';"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('specialist.diets.create', ['consultation_id' => $consultation->consultation_id, 'client_id' => $user->user_id]) }}" 
                                                   title="إضافة حمية"
                                                   style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: var(--bg-gray-dark); color: var(--primary-color); border-radius: var(--radius-md); transition: all 0.2s; text-decoration: none;"
                                                   onmouseover="this.style.background='var(--primary-color)'; this.style.color='white';"
                                                   onmouseout="this.style.background='var(--bg-gray-dark)'; this.style.color='var(--primary-color)';"
                                                >
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            @endif
                                        @else
                                            <span style="font-size: 0.8rem; color: var(--text-light); padding: 6px 12px; background: #f3f4f6; border-radius: var(--radius-md);">
                                                مغلقة
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- المودالات --}}
@foreach($consultations as $consultation)
    @php
        $user = $consultation->client;
        $client = $user->client ?? null;
        $hasData = $client && $client->bodyData && $client->lifestyle;
    @endphp
    <div id="modal-{{ $consultation->consultation_id }}" class="modal-overlay">
        <div class="modal-content">
            {{-- رأس المودال --}}
            {{-- رأس المودال --}}
            <div style="background: linear-gradient(135deg, #6B8E23 0%, #556B2F 100%); padding: var(--spacing-xl); color: white; position: relative;">
                <button onclick="closeUserModal('modal-{{ $consultation->consultation_id }}')" 
                        style="position: absolute; top: 15px; left: 15px; background: rgba(255,255,255,0.2); border: none; width: 35px; height: 35px; border-radius: 50%; font-size: 1.3rem; cursor: pointer; color: white; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" 
                        onmouseover="this.style.background='rgba(255,255,255,0.3)'" 
                        onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    &times;
                </button>
                
                <div style="text-align: center; padding: 0 var(--spacing-lg);">
                    <img src="{{ $user->photo_url ? Storage::url($user->photo_url) : 'https://ui-avatars.com/api/?name='.urlencode($user->Fname.' '.$user->Lname).'&background=random' }}" 
                         alt="{{ $user->Fname }}" 
                         style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.2); margin-bottom: var(--spacing-sm);"
                    >
                    <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700; line-height: 1.3;">{{ $user->Fname }} {{ $user->Lname }}</h2>
                </div>
            </div>

            {{-- جسم المودال --}}
            <div class="modal-body-content" style="padding: var(--spacing-xl);">
                @if(!$hasData)
                    <div class="data-card" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border: 2px solid #fca5a5;">
                        <div style="text-align: center;">
                            <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--spacing-md);">
                                <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 1.8rem;"></i>
                            </div>
                            <p style="color: #991b1b; margin-bottom: var(--spacing-md); font-weight: 600; font-size: 1.1rem; line-height: 1.4;">
                                لم يقم المستخدم بإدخال بياناته الصحية بعد
                            </p>
                            <button onclick="requestDataCompletion({{ $user->user_id }}, 'modal-{{ $consultation->consultation_id }}')" 
                                    class="btn-hover-effect"
                                    style="background: #ef4444; border: none; color: white; padding: 12px 30px; font-size: 1rem; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); cursor: pointer;"
                            >
                                <i class="fas fa-paper-plane" style="margin-left: 5px;"></i>
                                طلب إكمال البيانات
                            </button>
                        </div>
                    </div>
                @else
                    {{-- بيانات الجسم ونمط الحياة --}}
                    <div class="modal-body-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-lg); margin-bottom: var(--spacing-lg);">
                        {{-- بطاقة بيانات الجسم --}}
                        <div class="data-card" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border: 2px solid #93c5fd;">
                            <h4 style="color: #1e40af;">
                                <i class="fas fa-user-circle"></i>
                                بيانات الجسم
                            </h4>
                            <div>
                                <div class="data-item">
                                    <span class="data-label">العمر:</span>
                                    <span class="data-value">{{ $client->bodyData->age ?? '-' }} سنة</span>
                                </div>
                                <div class="data-item">
                                    <span class="data-label">الوزن:</span>
                                    <span class="data-value">{{ $client->bodyData->weight ?? '-' }} كجم</span>
                                </div>
                                <div class="data-item">
                                    <span class="data-label">الطول:</span>
                                    <span class="data-value">{{ $client->bodyData->height ?? '-' }} سم</span>
                                </div>
                                <div class="data-item">
                                    <span class="data-label">الجنس:</span>
                                    <span class="data-value">{{ $client->bodyData->gender ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- بطاقة نمط الحياة --}}
                        <div class="data-card" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 2px solid #6ee7b7;">
                            <h4 style="color: #065f46;">
                                <i class="fas fa-heartbeat"></i>
                                نمط الحياة
                            </h4>
                            <div>
                                <div class="data-item">
                                    <span class="data-label">النشاط:</span>
                                    <span class="data-value">{{ $client->lifestyle->activity_level ?? '-' }}</span>
                                </div>
                                <div class="data-item">
                                    <span class="data-label">ساعات النوم:</span>
                                    <span class="data-value">{{ $client->lifestyle->sleep_hours ?? '-' }}</span>
                                </div>
                                <div class="data-item">
                                    <span class="data-label">طبيعة العمل:</span>
                                    <span class="data-value">{{ $client->lifestyle->job_type ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- بطاقة الادوية الطبية  --}}
                    <div class="data-card" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #fcd34d;">
                        <h4 style="color: #92400e;">
                            <i class="fas fa-notes-medical"></i>
                            الادوية الطبية 
                        </h4>
                        
                        {{-- الأمراض المزمنة --}}
                        <div style="margin-bottom: var(--spacing-md);">
                            <p style="margin: 0 0 8px 0; color: #78350f; font-weight: 600;">الأمراض المزمنة:</p>
                            <div class="tag-container">
                                @forelse($client->chronicDiseases as $disease)
                                    <span class="tag" style="background: #fee2e2; color: #b91c1c;">{{ $disease->name }}</span>
                                @empty
                                    <span style="color: #64748b; font-style: italic;">لا يوجد</span>
                                @endforelse
                            </div>
                        </div>
                        
                        {{-- الحساسية --}}
                        <div>
                            <p style="margin: 0 0 8px 0; color: #78350f; font-weight: 600;">الحساسية:</p>
                            <div class="tag-container">
                                @forelse($client->allergies as $allergy)
                                    <span class="tag" style="background: #fef3c7; color: #b45309;">{{ $allergy->name }}</span>
                                @empty
                                    <span style="color: #64748b; font-style: italic;">لا يوجد</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- تذييل المودال --}}
            <div style="padding: var(--spacing-lg); background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: var(--spacing-sm);">
                <button onclick="closeUserModal('modal-{{ $consultation->consultation_id }}')" 
                        style="padding: 8px 20px; background: white; border: 1px solid #d1d5db; color: #374151; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#f3f4f6'"
                        onmouseout="this.style.background='white'">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
    
    {{-- مودال إضافة نص الإشعار --}}
    <div id="notify-modal-{{ $user->user_id }}" class="modal-overlay">
        <div class="modal-content" style="max-width: 500px;">
            <div style="padding: var(--spacing-lg); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: var(--text-dark);">إرسال إشعار مجدول</h3>
                <button onclick="closeUserModal('notify-modal-{{ $user->user_id }}')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-light);">&times;</button>
            </div>
            <div class="modal-body-content" style="padding: var(--spacing-lg);">
                <form id="notify-form-{{ $user->user_id }}" onsubmit="event.preventDefault(); submitNotification('{{ $user->user_id }}')">
                    <div style="margin-bottom: var(--spacing-md);">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-dark);">العنوان</label>
                        <input type="text" name="title" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);" placeholder="عنوان الإشعار">
                    </div>
                    <div style="margin-bottom: var(--spacing-md);">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-dark);">نص الإشعار</label>
                        <textarea name="message" required class="form-control" rows="3" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);" placeholder="اكتب نص الإشعار هنا..."></textarea>
                    </div>
                    <div style="margin-bottom: var(--spacing-md);">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-dark);">وقت الظهور</label>
                        <input type="datetime-local" name="scheduled_for" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <small style="color: var(--text-light); display: block; margin-top: 5px;">سيظهر الإشعار للمستخدم في هذا الوقت</small>
                    </div>
                    <div style="text-align: right;">
                        <button type="button" onclick="closeUserModal('notify-modal-{{ $user->user_id }}')" style="padding: 8px 16px; background: white; border: 1px solid #d1d5db; border-radius: 8px; margin-left: 8px; cursor: pointer;">إلغاء</button>
                        <button type="submit" style="padding: 8px 16px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">جدولة الإشعار</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<script>
    // دالة لفتح المودال
    function openUserModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function openNotificationModal(modalId) {
        openUserModal(modalId);
        // Set default datetime to now + 1 hour
        const modal = document.getElementById(modalId);
        const dateInput = modal.querySelector('input[type="datetime-local"]');
        if (dateInput && !dateInput.value) {
            const now = new Date();
            now.setHours(now.getHours() + 1);
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            dateInput.value = now.toISOString().slice(0, 16);
        }
    }

    // دالة لإغلاق المودال
    function closeUserModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // إغلاق المودال بالضغط على زر Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('.modal-overlay');
            modals.forEach(modal => {
                modal.style.display = 'none';
            });
            document.body.style.overflow = 'auto';
        }
    });

    // إغلاق المودال عند الضغط خارج المحتوى
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    // دالة لعرض رسالة تنبيه
    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert-message alert-${type}`;
        alertDiv.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
            ${message}
        `;
        
        document.body.appendChild(alertDiv);
        
        // إزالة الرسالة بعد 3 ثواني
        setTimeout(() => {
            alertDiv.style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 300);
        }, 3000);
    }

    // دالة لطلب إكمال البيانات
    async function requestDataCompletion(userId, modalId) {
        // ... (existing code)
    }

    // دالة إرسال الإشعار المجدول
    async function submitNotification(userId) {
        const form = document.getElementById(`notify-form-${userId}`);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

        try {
            const formData = new FormData(form);
            const data = {
                user_id: userId,
                title: formData.get('title'),
                message: formData.get('message'),
                scheduled_for: formData.get('scheduled_for')
            };

            const response = await fetch('{{ route("specialist.notifications.schedule") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showAlert('تم جدولة الإشعار بنجاح', 'success');
                closeUserModal(`notify-modal-${userId}`);
                form.reset();
            } else {
                throw new Error(result.message || 'فشل في الجدولة');
            }
        } catch (error) {
            console.error(error);
            showAlert(error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    // تهيئة عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        // التأكد من أن المودالات مخفية
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
        
        // تحسين عرض الجدول على الشاشات الصغيرة
        function adjustTableForMobile() {
            const tableContainer = document.querySelector('.card > div');
            if (window.innerWidth < 768 && tableContainer) {
                tableContainer.style.overflowX = 'auto';
            }
        }
        
        adjustTableForMobile();
        window.addEventListener('resize', adjustTableForMobile);
    });
</script>
@endsection