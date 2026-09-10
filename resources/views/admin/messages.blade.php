@extends('layouts.admin_app')

@section('title', 'الرسائل')

@push('styles')
<style>
    :root {
        --olive-dark: #6B8E23;
        --olive-medium: #9CAF88;
        --olive-light: #D4E5C4;
        --olive-very-light: #F5F9F0;
        --text-dark: #1F2937;
        --text-light: #6B7280;
        --border-color: #E5E7EB;
    }
    
    .chat-container {
        display: flex;
        height: calc(100vh - 120px);
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border: 1px solid var(--border-color);
    }
    .chat-sidebar {
        width: 320px;
        border-left: 1px solid var(--border-color); /* RTL */
        display: flex;
        flex-direction: column;
        background: #F9FAFB;
    }
    .chat-search {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        background: white;
    }
    .chat-search input {
        width: 100%;
        box-sizing: border-box; /* Fix overflow issues */
        padding: 10px 15px; /* Slightly reduced padding */
        border: 1px solid var(--border-color);
        border-radius: 20px;
        background: #F9FAFB;
        font-family: 'Cairo', sans-serif;
        font-size: 13px; /* Slightly smaller font */
        transition: all 0.3s ease;
    }
    .chat-search input:focus {
        outline: none;
        border-color: var(--olive-medium);
        background: white;
        box-shadow: 0 0 0 4px var(--olive-very-light);
    }
    .conversations-list {
        flex: 1;
        overflow-y: auto;
    }
    .conversation-item {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid var(--border-color);
    }
    .conversation-item:hover, .conversation-item.active {
        background: white;
        border-right: 4px solid var(--olive-medium);
    }
    .conversation-item.active {
        background: var(--olive-very-light);
    }
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--olive-light);
        color: var(--olive-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 0; /* Handled by gap */
        font-weight: bold;
        font-size: 18px;
        flex-shrink: 0;
    }
    .user-info {
        flex: 1;
        overflow: hidden;
    }
    .user-name {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 6px;
        font-size: 15px;
    }
    .last-message {
        font-size: 13px;
        color: var(--text-light);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: white;
    }
    .chat-header {
        padding: 0 30px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        font-weight: 600;
        color: var(--text-dark);
        background: white;
        height: 80px;
    }
    .messages-area {
        flex: 1;
        padding: 30px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
        background: #FDFDFD;
        background-image: radial-gradient(#E5E7EB 1px, transparent 1px);
        background-size: 20px 20px;
    }
    .message {
        max-width: 65%;
        padding: 12px 20px;
        border-radius: 18px;
        font-size: 15px;
        line-height: 1.6;
        position: relative;
        word-wrap: break-word;
        white-space: pre-wrap;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .message.sent {
        align-self: flex-end;
        background: var(--olive-medium);
        color: white;
        border-bottom-left-radius: 4px;
    }
    .message.received {
        align-self: flex-start;
        background: white;
        border: 1px solid var(--border-color);
        color: var(--text-dark);
        border-bottom-right-radius: 4px;
    }
    .message-time {
        font-size: 11px;
        margin-top: 6px;
        opacity: 0.8;
        text-align: left;
    }
    .message.sent .message-time { color: rgba(255,255,255,0.9); text-align: right; }
    
    .chat-input-area {
        padding: 20px 30px;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 20px;
        background: white;
    }
    .chat-input {
        flex: 1;
        padding: 15px 25px;
        border: 1px solid var(--border-color);
        border-radius: 30px;
        outline: none;
        font-family: 'Cairo', sans-serif;
        background: #F9FAFB;
        transition: all 0.2s;
        font-size: 15px;
    }
    .chat-input:focus {
        background: white;
        border-color: var(--olive-medium);
        box-shadow: 0 0 0 4px var(--olive-light);
    }
    .btn-send {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--olive-medium);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(107, 142, 35, 0.3);
    }
    .btn-send:hover {
        background: var(--olive-dark);
        transform: scale(1.05);
        box-shadow: 0 6px 15px rgba(107, 142, 35, 0.4);
    }
    .btn-attach {
        color: var(--text-light);
        cursor: pointer;
        font-size: 24px;
        padding: 5px;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-attach:hover {
        color: var(--olive-medium);
        background: var(--olive-very-light);
        border-radius: 50%;
    }
    .message-image {
        max-width: 300px;
        border-radius: 12px;
        margin-top: 8px;
        cursor: pointer;
        border: 1px solid rgba(0,0,0,0.1);
    }
    .no-chat-selected {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--text-light);
    }
    
    /* Scrollbar */
    .conversations-list::-webkit-scrollbar,
    .messages-area::-webkit-scrollbar {
        width: 6px;
    }
    .conversations-list::-webkit-scrollbar-thumb,
    .messages-area::-webkit-scrollbar-thumb {
        background-color: #D1D5DB;
        border-radius: 3px;
    }
    
    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
        color: var(--text-light);
        height: 100%;
    }
    .empty-state i {
        font-size: 50px;
        color: var(--olive-light);
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="chat-container">
    <div class="chat-sidebar">
        <div class="chat-search">
            <input type="text" placeholder="بحث عن مستخدم..." id="searchUser">
        </div>
        <div class="conversations-list" id="conversationsList">
            <!-- Loading -->
            <div style="padding: 20px; text-align: center; color: var(--text-light);">
                <i class="fas fa-spinner fa-spin"></i> جاري التحميل...
            </div>
        </div>
        
        <!-- Empty State Container (Hidden by default) -->
        <div id="emptyState" class="empty-state" style="display: none;">
            <i class="fas fa-inbox"></i>
            <h3>لا توجد محادثات</h3>
            <p style="font-size: 13px;">لم تتلق أي رسائل بعد</p>
        </div>
    </div>
    
    <div class="chat-main" id="chatMain">
        <div class="no-chat-selected">
            <i class="fas fa-comments" style="font-size: 60px; margin-bottom: 20px; color: var(--olive-light);"></i>
            <p style="font-size: 18px; color: var(--text-dark);">اختر مستخدماً لبدء المحادثة</p>
        </div>
    </div>
</div>

<!-- Template for Chat Interface -->
<template id="chatInterfaceTemplate">
    <div class="chat-header">
        <div class="user-avatar" id="headerAvatar" style="width: 35px; height: 35px; margin-left: 10px; font-size: 14px;"></div>
        <span id="headerName" style="font-size: 16px;">User Name</span>
    </div>
    <div class="messages-area" id="messagesArea"></div>
    <div class="chat-input-area">
        <label for="fileInput" class="btn-attach" title="إرفاق ملف">
            <i class="fas fa-paperclip"></i>
        </label>
        <input type="file" id="fileInput" hidden>
        
        <div style="flex: 1; position: relative; margin-right: 10px;">
            <input type="text" class="chat-input" id="messageInput" placeholder="اكتب رسالتك هنا..." style="width: 100%; box-sizing: border-box; padding-right: 60px;">
            <div id="charCounter" style="position: absolute; left: 15px; bottom: 12px; font-size: 11px; color: var(--text-light); transition: color 0.3s; pointer-events: none;">0/1000</div>
            <div id="lenError" style="position: absolute; top: -35px; right: 0; background: #FEF2F2; color: #EF4444; border: 1px solid #FCA5A5; font-size: 12px; padding: 4px 10px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: none; white-space: nowrap; z-index: 10; align-items: center; gap: 6px;">
                <i class="fas fa-exclamation-circle"></i> لا يمكن أن تكون الرسالة أكثر من 1000 حرف
            </div>
        </div>

        <button class="btn-send" id="btnSend" onclick="sendMessage()">
            <i class="fas fa-paper-plane" style="font-size: 16px;"></i>
        </button>
    </div>
</template>

@endsection

@push('scripts')
<script>
    let currentUserId = null;
    let pollingInterval = null;
    let sidebarPollingInterval = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadConversations();
        
        // Auto-refresh conversations list every 5 seconds
        sidebarPollingInterval = setInterval(loadConversations, 5000);
        
        // Search filter
        const searchInput = document.getElementById('searchUser');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.conversation-item').forEach(item => {
                    const name = item.querySelector('.user-name').textContent.toLowerCase();
                    item.style.display = name.includes(term) ? 'flex' : 'none';
                });
            });
        }

        // Check for deep link (?user=ID)
        const urlParams = new URLSearchParams(window.location.search);
        const targetUserId = urlParams.get('user');
        if (targetUserId) {
            // Fetch user specific details to open chat immediately even if not in recent list
            fetch(`/chat/user-info/${targetUserId}`) 
                .then(res => {
                     if(!res.ok) throw new Error('User not found');
                     return res.json();
                })
                .then(user => {
                    // Normalize user object if needed to match conversation structure
                    // The API endpoint might return different structure, let's assume it returns { user_id, Fname, Lname, ... }
                    // We need to ensure we have the right fields for openChat
                    const chatUser = {
                        user_id: user.user_id || user.id, // Handle potential field diffs
                        Fname: user.Fname,
                        Lname: user.Lname,
                        // Add other fields if openChat needs them
                    };
                    openChat(chatUser);
                })
                .catch(err => {
                    console.log('Could not auto-load user chat:', err);
                    // Fallback: wait for loadConversations and try to click
                    setTimeout(() => {
                        const item = document.querySelector(`.conversation-item[onclick*="${targetUserId}"]`); // messy
                        // better to just let the user pick or rely on list
                    }, 1000);
                });
        }
    });

    function loadConversations() {
        fetch('{{ route("chat.conversations") }}')
            .then(res => res.json())
            .then(users => {
                const list = document.getElementById('conversationsList');
                const emptyState = document.getElementById('emptyState');
                
                // If no users, show empty state
                if (users.length === 0) {
                    list.style.display = 'none';
                    emptyState.style.display = 'flex';
                    return;
                }
                
                list.style.display = 'block';
                emptyState.style.display = 'none';
                list.innerHTML = '';
                
                users.forEach(user => {
                    const div = document.createElement('div');
                    div.className = `conversation-item ${currentUserId === user.user_id ? 'active' : ''}`;
                    div.onclick = () => openChat(user);
                    
                    const initial = user.Fname ? user.Fname.charAt(0).toUpperCase() : 'U';
                    
                    // Format last message text
                    let lastMsgText = 'بدء محادثة';
                    if (user.last_message) {
                        const isMe = user.last_message.sender_id === {{ Auth::id() }};
                        const prefix = isMe ? 'أنت: ' : '';
                        const content = user.last_message.message || '📎 ملف مرفق';
                        lastMsgText = prefix + content;
                    }

                    // Unread badge
                    const unreadBadge = user.unread_count > 0 ? 
                        `<span style="background: #ef4444; color: white; border-radius: 50%; min-width: 20px; height: 20px; padding: 0 5px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; margin-right: auto;">${user.unread_count}</span>` : 
                        '';
                    
                    div.innerHTML = `
                        <div class="user-avatar">${initial}</div>
                        <div class="user-info">
                            <div class="user-name">${user.Fname} ${user.Lname || ''}</div>
                            <div class="last-message">${lastMsgText}</div>
                        </div>
                        ${unreadBadge}
                    `;
                    list.appendChild(div);
                });
            })
            .catch(error => console.error('Error loading conversations:', error));
    }

    function openChat(user) {
        if (currentUserId === user.user_id) return; // Prevent reload if same user logic if wanted, but reloading helps refresh

        currentUserId = user.user_id;
        
        // Update active state in sidebar immediately for better UX
        document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
        // We defer re-adding active class until list reload or handle it via DOM traversal if eager
        
        // Setup Chat UI
        const chatMain = document.getElementById('chatMain');
        const template = document.getElementById('chatInterfaceTemplate');
        chatMain.innerHTML = '';
        chatMain.appendChild(template.content.cloneNode(true));
        
        document.getElementById('headerName').textContent = `${user.Fname} ${user.Lname || ''}`;
        document.getElementById('headerAvatar').textContent = user.Fname ? user.Fname.charAt(0).toUpperCase() : 'U';
        
        // Check permission first (optional UI enhancement)
        fetch(`/chat/can-send/${user.user_id}`)
            .then(res => res.json())
            .then(data => {
                if (!data.can_send) {
                     const inputArea = document.querySelector('.chat-input-area');
                     if (inputArea) {
                         inputArea.innerHTML = `<div style="text-align: center; width: 100%; color: #ef4444; font-size: 14px;"><i class="fas fa-lock"></i> ${data.message}</div>`;
                     }
                }
            });

        // File input listener
        const fileInput = document.getElementById('fileInput');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if(this.files.length > 0) sendMessage();
            });
        }

        // Enter key listener
        const msgInput = document.getElementById('messageInput');
        const charCounter = document.getElementById('charCounter');
        const lenError = document.getElementById('lenError');
        const btnSend = document.getElementById('btnSend');

        if (msgInput) {
            // Validation & Counter
            msgInput.addEventListener('input', function(e) {
                const len = this.value.length;
                
                // Update counter
                if (charCounter) charCounter.textContent = `${len}/1000`;
                
                // Check limit
                if (len > 1000) {
                    this.style.borderColor = '#EF4444';
                    this.style.boxShadow = '0 0 0 3px #FEF2F2';
                    if (charCounter) charCounter.style.color = '#EF4444';
                    if (lenError) lenError.style.display = 'flex';
                    if (btnSend) {
                        btnSend.disabled = true;
                        btnSend.style.opacity = '0.5';
                        btnSend.style.cursor = 'not-allowed';
                    }
                } else {
                    this.style.borderColor = '';
                    this.style.boxShadow = '';
                    if (charCounter) charCounter.style.color = '';
                    if (lenError) lenError.style.display = 'none';
                    if (btnSend) {
                        btnSend.disabled = false;
                        btnSend.style.opacity = '1';
                        btnSend.style.cursor = 'pointer';
                    }
                }
            });

            msgInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    if (this.value.length > 1000) {
                        e.preventDefault();
                        return;
                    }
                    sendMessage();
                }
            });
            msgInput.focus();
        }

        loadMessages();
        
        // Start polling for messages (every 3 seconds)
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(() => {
            loadMessages();
            loadConversations(); // Update sidebar too
        }, 3000);
    }

    function loadMessages() {
        if (!currentUserId) return;
        
        fetch(`/chat/messages/${currentUserId}`)
            .then(res => res.json())
            .then(data => {
                // Handle pagination object if returned, or array
                const messages = data.data ? data.data : data; 
                
                const area = document.getElementById('messagesArea');
                if (!area) return;

                // Scroll detection (simple: if at bottom or empty)
                // const shouldScroll = area.scrollTop + area.clientHeight >= area.scrollHeight - 50 || area.innerHTML === '';
                
                area.innerHTML = '';
                const myId = {{ Auth::id() }};
                
                // If Array is reverse chronological (newest first), we need to reverse it to display oldest at top
                // Assuming API returns newest first (desc) as per controller
                const renderedMessages = Array.isArray(messages) ? messages.slice().reverse() : [];
                
                if (renderedMessages.length === 0) {
                    area.innerHTML = '<div style="text-align:center; color:#ccc; margin-top:50px;">لا توجد رسائل سابقة. ابدأ المحادثة الآن!</div>';
                    return;
                }

                renderedMessages.forEach(msg => {
                    const div = document.createElement('div');
                    div.className = `message ${msg.sender_id === myId ? 'sent' : 'received'}`;
                    
                    let content = '';
                    if (msg.message) content += `<div>${msg.message}</div>`;
                    if (msg.file_url) {
                        if (msg.file_type === 'image') {
                            content += `<img src="/storage/${msg.file_url}" class="message-image" onclick="window.open(this.src)">`;
                        } else {
                            content += `<div style="margin-top:5px; background: rgba(0,0,0,0.05); padding: 5px 10px; border-radius: 5px;">
                                <a href="/storage/${msg.file_url}" target="_blank" style="color:inherit; text-decoration:none; display: flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-file-alt" style="font-size: 20px;"></i> 
                                    <span>ملف مرفق</span>
                                </a>
                            </div>`;
                        }
                    }
                    
                    const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    content += `<div class="message-time">${time}</div>`;
                    
                    div.innerHTML = content;
                    area.appendChild(div);
                });
                
                // Construct simple scroll to bottom for now
                area.scrollTop = area.scrollHeight;
            });
    }

    function sendMessage() {
        const input = document.getElementById('messageInput');
        const fileInput = document.getElementById('fileInput');
        const message = input.value.trim();
        const file = fileInput.files[0];
        
        if (!message && !file) return;
        
        const formData = new FormData();
        formData.append('receiver_id', currentUserId);
        if (message) formData.append('message', message);
        if (file) formData.append('file', file);
        
        // Clear inputs immediately
        input.value = '';
        fileInput.value = '';
        
        // Show temp message (Optimistic UI) - Optional
        
        fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { throw err; });
            }
            return res.json();
        })
        .then(data => {
            loadMessages(); // Reload immediately
            loadConversations(); // Update sidebar last message
        })
        .catch(err => {
            console.error(err);
            alert('فشل إرسال الرسالة: ' + (err.error || 'حدث خطأ غير متوقع'));
        });
    }
</script>
@endpush
