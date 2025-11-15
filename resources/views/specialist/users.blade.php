@extends('layouts.specialist_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'User Management')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
    {{-- إضافة style.css الخاص بـ Ucss (إذا كان مختلفاً عن general.css) --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> 
@endpush


{{-- 3. هذا هو المح     توى المتغير --}}
@section('content')
    <div class="users-section">
        <div class="section-header">
            <h1 class="section-title" data-i18n="userManagement">Diet Client Management</h1>
            {{-- إصلاح رابط الزر --}}
            <a href="{{ url('specialist/users/add') }}" class="btn-primary add-user-btn">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="addNewClient">Add New Client</span>
            </a>
        </div>

        <div class="card users-table-card">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th data-i18n="clientName">Client Name</th>
                        <th data-i18n="dietPlan">Diet Plan</th>
                        <th data-i18n="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="user-row">
                        <td data-label="الصورة">
                            {{-- إصلاح مسار الصورة --}}
                            <img src="{{ asset('images/user_male_1.jpg') }}" alt="Client Photo" class="client-avatar">
                        </td>
                        <td class="client-name-cell" data-label="اسم العميل">
                            {{-- إصلاح رابط البروفايل (استخدام 1 كمثال) --}}
                            <a href="{{ url('specialist/users/profile/1') }}" class="client-name-link">Ahmed Mohammed</a>
                        </td>
                        <td data-label="النظام الغذائي">
                            <span class="diet-tag diet-keto">Keto Diet 🥩</span>
                        </td>
                        <td data-label="الإجراءات">
                            {{-- إصلاح روابط الأزرار --}}
                            <a href="{{ url('specialist/messages') }}" class="action-btn message-btn" title="Message Client">
                                <i class="fas fa-envelope" style="color: var(--blue-primary);"></i>
                            </a>
                            <a href="{{ url('specialist/users/edit/1') }}" class="action-btn edit-btn" title="Edit Client Data">
                                <i class="fas fa-edit" style="color: var(--olive-dark);"></i>
                            </a>
                            <button class="action-btn delete-btn" title="Delete Client">
                                <i class="fas fa-trash" style="color: var(--red-accent);"></i>
                            </button>
                        </td>
                    </tr>
                    
                    <tr class="user-row">
                        <td data-label="الصورة">
                            {{-- إصلاح مسار الصورة --}}
                            <img src="{{ asset('images/user_female_1.jpg') }}" alt="Client Photo" class="client-avatar">
                        </td>
                        <td class="client-name-cell" data-label="اسم العميل">
                            {{-- إصلاح رابط البروفايل (استخدام 2 كمثال) --}}
                            <a href="{{ url('specialist/users/profile/2') }}" class="client-name-link">Fatima Ali</a>
                        </td>
                        <td data-label="النظام الغذائي">
                            <span class="diet-tag diet-lowcarb">Low-Carb Diet 🍞</span>
                        </td>
                        <td data-label="الإجراءات">
                            <a href="{{ url('specialist/messages') }}" class="action-btn message-btn" title="Message Client">
                                <i class="fas fa-envelope" style="color: var(--blue-primary);"></i>
                            </a>
                            <a href="{{ url('specialist/users/edit/2') }}" class="action-btn edit-btn" title="Edit Client Data">
                                <i class="fas fa-edit" style="color: var(--olive-dark);"></i>
                            </a>
                            <button class="action-btn delete-btn" title="Delete Client">
                                <i class="fas fa-trash" style="color: var(--red-accent);"></i>
                            </button>
                        </td>
                    </tr>

                    <tr class="user-row">
                        <td data-label="الصورة">
                            {{-- إصلاح مسار الصورة --}}
                            <img src="{{ asset('images/user_male_2.jpg') }}" alt="Client Photo" class="client-avatar">
                        </td>
                        <td class="client-name-cell" data-label="اسم العميل">
                            {{-- إصلاح رابط البروفايل (استخدام 3 كمثال) --}}
                            <a href="{{ url('specialist/users/profile/3') }}" class="client-name-link">Khaled Nasser</a>
                        </td>
                        <td data-label="النظام الغذائي">
                            <span class="diet-tag diet-vegan">Vegan Diet 🌱</span>
                        </td>
                        <td data-label="الإجراءات">
                            <a href="{{ url('specialist/messages') }}" class="action-btn message-btn" title="Message Client">
                                <i class="fas fa-envelope" style="color: var(--blue-primary);"></i>
                            </a>
                            <a href="{{ url('specialist/users/edit/3') }}" class="action-btn edit-btn" title="Edit Client Data">
                                <i class="fas fa-edit" style="color: var(--olive-dark);"></i>
                            </a>
                            <button class="action-btn delete-btn" title="Delete Client">
                                <i class="fas fa-trash" style="color: var(--red-accent);"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection