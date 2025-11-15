@extends('layouts.specialist_app')

{{-- 1. تحديد عنوان الصفحة --}}
@section('title', 'Messages')

{{-- 2. إضافة CSS الخاص بالصفحة + الـ CSS المضمّن --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles-2.css') }}">
    <style>
        .messages-container {
            display: flex;
            gap: 12px;
            height: calc(100vh - 100px); /* تعديل ليتناسب مع ارتفاع الصفحة */
        }
        .messages-list {
            width: 280px;
            background-color: white;
            border-radius: 8px;
            overflow-y: auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .message-item {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .message-item:hover { background-color: #F9FAFB; }
        .message-item.active {
            background-color: var(--olive-very-light);
            border-left: 3px solid var(--olive-dark);
        }
        .message-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .message-item-name { font-weight: 600; font-size: 12px; color: var(--text-dark); }
        .message-item-time { font-size: 10px; color: var(--text-light); }
        .message-item-preview {
            font-size: 11px;
            color: var(--text-light);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .messages-chat {
            flex: 1;
            background-color: white;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .messages-chat-header {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .messages-chat-title { font-weight: 600; font-size: 13px; color: var(--text-dark); }
        .messages-chat-body {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .message-bubble { max-width: 70%; padding: 10px 12px; border-radius: 8px; font-size: 12px; line-height: 1.4; }
        .message-bubble.sent {
            align-self: flex-end;
            background-color: var(--olive-medium);
            color: white;
        }
        .message-bubble.received {
            align-self: flex-start;
            background-color: #F3F4F6;
            color: var(--text-dark);
        }
        .messages-chat-footer {
            padding: 12px;
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 8px;
        }
        .message-input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            font-size: 12px;
            font-family: inherit;
        }
        .message-send-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--olive-dark);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .message-send-btn:hover { background-color: var(--olive-medium); transform: scale(1.05); }
        @media (max-width: 768px) {
            .messages-container { flex-direction: column; height: auto; }
            .messages-list { width: 100%; max-height: 200px; }
            .message-bubble { max-width: 90%; }
        }
    </style>
@endpush


{{-- 3. هذا هو المحتوى المتغير --}}
@section('content')
    <div class="messages-container">
        <div class="messages-list">
            <div class="message-item active" data-chat="1">
                <div class="message-item-header">
                    <div class="message-item-name">Sarah Johnson</div>
                    <div class="message-item-time">10:30 AM</div>
                </div>
                <div class="message-item-preview">Thank you for the diet plan...</div>
            </div>
            <div class="message-item" data-chat="2">
                <div class="message-item-header">
                    <div class="message-item-name">John Smith</div>
                    <div class="message-item-time">9:15 AM</div>
                </div>
                <div class="message-item-preview">I have a question about my meal...</div>
            </div>
            </div>

        <div class="messages-chat">
            <div class="messages-chat-header">
                <div class="messages-chat-title">Sarah Johnson</div>
                <button class="icon-button" style="width: 28px; height: 28px; font-size: 14px;">⋮</button>
            </div>
            <div class="messages-chat-body">
                <div class="message-bubble received">
                    Hi Dr. Afnan, I wanted to thank you for the personalized diet plan. It's been really helpful!
                </div>
                <div class="message-bubble sent">
                    You're welcome, Sarah! I'm glad it's working well for you. Keep up the great work!
                </div>
                </div>
            <div class="messages-chat-footer">
                <input type="text" class="message-input" placeholder="Type your message...">
                <button class="message-send-btn">📤</button>
            </div>
        </div>
    </div>
@endsection


{{-- 4. إضافة الـ JS المضمن الخاص بهذه الصفحة --}}
@push('scripts')
    <script>
        // Messages functionality
        (function() { // تغليف الكود
            document.addEventListener('DOMContentLoaded', function() {
                const messageItems = document.querySelectorAll('.message-item');
                
                if (messageItems.length > 0) {
                    messageItems.forEach(item => {
                        item.addEventListener('click', function() {
                            messageItems.forEach(i => i.classList.remove('active'));
                            this.classList.add('active');
                            // (يمكنك إضافة كود هنا لتغيير المحادثة في نافذة الدردشة)
                        });
                    });
                }
            });
        })();
    </script>
@endpush