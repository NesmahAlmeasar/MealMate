@extends('layouts.specialist_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'Notifications')

{{-- 2. إضافة ملف الـ CSS الخاص بهذه الصفحة --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')
    <a href="{{ url('specialist/dashboard') }}" class="back-button" data-i18n="back">
        <i class="fas fa-arrow-left"></i>
        Back to Previous Screen
    </a>
    
    <div class="notifications-section">
        <h1 class="section-title" data-i18n="notificationsTitle"><i class="fas fa-bell"></i> New Notifications</h1>
        
        <div class="card notifications-list-card">
            <div class="notification-item unread">
                <div class="notification-icon" style="background-color: var(--green-accent);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="notification-content">
                    <p class="notification-text"><strong>New client added:</strong> "Fatima Ali" has been successfully registered.</p>
                    <span class="notification-time">5 minutes ago</span>
                </div>
                <button class="mark-read-btn" title="Mark as read"><i class="fas fa-eye"></i></button>
            </div>

            <div class="notification-item unread">
                <div class="notification-icon" style="background-color: var(--red-accent);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="notification-content">
                    <p class="notification-text"><strong>Health Alert:</strong> "Ahmed Mohammed" recorded a sharp drop in weight.</p>
                    <span class="notification-time">1 hour ago</span>
                </div>
                <button class="mark-read-btn" title="Mark as read"><i class="fas fa-eye"></i></button>
            </div>

            <div class="notification-item">
                <div class="notification-icon" style="background-color: var(--blue-primary);">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <div class="notification-content">
                    <p class="notification-text"><strong>New Message:</strong> You have an unread message from "Khaled Nasser".</p>
                    <span class="notification-time">1 day ago</span>
                </div>
                <button class="mark-read-btn" title="Mark as read"><i class="fas fa-eye-slash"></i></button>
            </div>

            <div class="no-notifications">
                <p>No further notifications.</p>
            </div>
        </div>
    </div>
@endsection