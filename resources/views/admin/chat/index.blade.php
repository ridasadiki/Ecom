@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex space-x-4">
        <!-- Users List -->
        <div class="w-1/4">
            <div class="bg-white rounded-lg shadow-lg">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Active Chats</h2>
                </div>
                <div id="users-list" class="divide-y divide-gray-200">
                    <!-- Users will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="w-3/4">
            <div class="bg-white rounded-lg shadow-lg">
                <div class="p-4 border-b border-gray-200">
                    <h2 id="chat-title" class="text-xl font-semibold text-gray-800">Select a chat to start</h2>
                </div>
                
                <div id="chat-messages" class="h-96 overflow-y-auto p-4 space-y-4">
                    <!-- Messages will be loaded here -->
                </div>

                <div class="p-4 border-t border-gray-200">
                    <form id="chat-form" class="flex space-x-4">
                        <input type="text" id="message-input" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Type your message..." disabled>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700" disabled>
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true
    });

    let currentUserId = null;
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatTitle = document.getElementById('chat-title');
    const usersList = document.getElementById('users-list');

    function loadUsers() {
        fetch('{{ route('admin.chat.users') }}')
            .then(response => response.json())
            .then(users => {
                usersList.innerHTML = users.map(user => `
                    <div class="p-4 hover:bg-gray-50 cursor-pointer user-item" data-user-id="${user.id}">
                        <h3 class="font-medium text-gray-800">${user.name}</h3>
                        <p class="text-sm text-gray-500">${user.email}</p>
                    </div>
                `).join('');

                document.querySelectorAll('.user-item').forEach(item => {
                    item.addEventListener('click', () => loadChat(item.dataset.userId));
                });
            });
    }

    function loadChat(userId) {
        currentUserId = userId;
        messageInput.disabled = false;
        chatForm.querySelector('button').disabled = false;

        fetch(`{{ url('admin/chat/messages') }}/${userId}`)
            .then(response => response.json())
            .then(messages => {
                chatMessages.innerHTML = messages.map(message => {
                    const isAdmin = message.type === 'admin';
                    return `
                        <div class="flex ${isAdmin ? 'justify-end' : 'justify-start'}">
                            <div class="max-w-sm ${isAdmin ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800'} rounded-lg px-4 py-2 shadow">
                                <p class="text-sm">${message.message}</p>
                                <p class="text-xs mt-1 ${isAdmin ? 'text-indigo-200' : 'text-gray-500'}">${message.created_at}</p>
                            </div>
                        </div>
                    `;
                }).join('');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });

        // Subscribe to user's channel
        pusher.unsubscribe(`private-chat.${currentUserId}`);
        const channel = pusher.subscribe(`private-chat.${currentUserId}`);
        
        channel.bind('App\\Events\\NewChatMessage', function(data) {
            const message = data.message;
            const isAdmin = message.type === 'admin';
            
            const messageHtml = `
                <div class="flex ${isAdmin ? 'justify-end' : 'justify-start'}">
                    <div class="max-w-sm ${isAdmin ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800'} rounded-lg px-4 py-2 shadow">
                        <p class="text-sm">${message.message}</p>
                        <p class="text-xs mt-1 ${isAdmin ? 'text-indigo-200' : 'text-gray-500'}">Just now</p>
                    </div>
                </div>
            `;
            
            chatMessages.insertAdjacentHTML('beforeend', messageHtml);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!messageInput.value.trim() || !currentUserId) return;

        fetch('{{ route('admin.chat.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: messageInput.value,
                user_id: currentUserId
            })
        });

        messageInput.value = '';
    });

    // Load initial users list
    loadUsers();
    
    // Refresh users list periodically
    setInterval(loadUsers, 30000);
});
</script>
@endpush
